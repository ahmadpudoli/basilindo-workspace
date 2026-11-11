<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\CRM\Domain\Models\CrmOpportunity;
use App\Modules\HRIS\Domain\Models\Employee;
use Core\Models\Project;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class OvertimeWorkReferenceService
{
    public function availableOpportunities(Employee $employee, User $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return CrmOpportunity::query()->with(['pipelineStage', 'closingStatus', 'project']);
        }

        $query = CrmOpportunity::query()->with(['pipelineStage', 'closingStatus', 'project']);

        if (! $user->can('view_crm_opportunity')) {
            return $query->whereRaw('1 = 0');
        }

        $companyIds = $user->companies()->pluck('companies.id');

        return CrmOpportunity::query()
            ->with(['pipelineStage', 'closingStatus', 'project'])
            ->where(function (Builder $company) use ($companyIds): void {
                $company->whereIn('company_id', $companyIds)
                    ->orWhereHas('account', fn (Builder $account) => $account->whereIn('company_id', $companyIds));
            });
    }

    public function resolve(Employee $employee, array $data, int $actorId): array
    {
        if (($data['overtime_type'] ?? 'internal') !== 'external_project') {
            return $this->emptyReference();
        }

        $type = $data['work_reference_type'] ?? $this->inferType($data);
        if ($type === 'none') {
            return $this->emptyReference();
        }

        $user = User::findOrFail($actorId);
        if ($type === 'project') {
            $projectId = app(OvertimeProjectService::class)->resolve($employee, $data, $actorId);
            $project = Project::query()->find($projectId);
            if (! $project) {
                throw ValidationException::withMessages(['project_id' => 'Project tidak tersedia atau tidak dapat Anda akses.']);
            }

            return [
                'work_reference_type' => 'project',
                'project_id' => (int) $project->getKey(),
                'opportunity_id' => null,
                'reference_name_snapshot' => $project->name,
                'reference_status_snapshot' => 'Project',
            ];
        }

        if (! $user->hasRole('super_admin') && ! $user->can('view_crm_opportunity')) {
            throw ValidationException::withMessages(['opportunity_id' => 'Anda tidak memiliki akses ke opportunity CRM.']);
        }

        $opportunity = $this->availableOpportunities($employee, $user)->whereKey($data['opportunity_id'] ?? null)->first();
        if (! $opportunity || Gate::forUser($user)->denies('view', $opportunity)) {
            throw ValidationException::withMessages(['opportunity_id' => 'Opportunity tidak tersedia atau tidak dapat Anda akses.']);
        }

        $linkedProject = $opportunity->project_id
            ? app(OvertimeProjectService::class)->available($employee, $user)->whereKey($opportunity->project_id)->first()
            : null;

        return [
            'work_reference_type' => 'opportunity',
            'project_id' => $linkedProject?->getKey(),
            'opportunity_id' => (string) $opportunity->getKey(),
            'reference_name_snapshot' => $opportunity->name,
            'reference_status_snapshot' => $this->statusLabel($opportunity),
        ];
    }

    public function statusLabel(CrmOpportunity $opportunity): string
    {
        $parts = $this->statusParts($opportunity);

        return "{$parts['classification']} · Stage: {$parts['stage']} · Closing: {$parts['closing']}";
    }

    /** @return array{classification: string, stage: string, closing: string} */
    public function statusParts(CrmOpportunity $opportunity): array
    {
        $stage = $opportunity->pipelineStage?->name ?: ($opportunity->stage ?: '—');
        $closing = $opportunity->closingStatus?->name ?: '—';
        $closingCode = mb_strtolower((string) ($opportunity->closingStatus?->code ?? ''));
        $closingName = mb_strtolower((string) ($opportunity->closingStatus?->name ?? ''));
        $classification = match (true) {
            in_array($closingCode, ['cancel', 'canceled', 'cancelled'], true) || str_contains($closingName, 'cancel') => 'Dibatalkan',
            $opportunity->closingStatus?->is_lost || $closingCode === 'lost' || str_contains($closingName, 'lost') => 'Lost',
            $opportunity->closingStatus?->is_ignored || $closingCode === 'ignored' || str_contains($closingName, 'ignor') => 'Diabaikan',
            $opportunity->pipelineStage?->is_won || $opportunity->pipelineStage?->is_delivery || $opportunity->closingStatus?->is_closed => 'Sales',
            default => 'Prospek',
        };

        return compact('classification', 'stage', 'closing');
    }

    private function inferType(array $data): string
    {
        if (filled($data['opportunity_id'] ?? null)) {
            return 'opportunity';
        }

        return filled($data['project_id'] ?? null) ? 'project' : 'none';
    }

    private function emptyReference(): array
    {
        return [
            'work_reference_type' => 'none',
            'project_id' => null,
            'opportunity_id' => null,
            'reference_name_snapshot' => null,
            'reference_status_snapshot' => null,
        ];
    }
}

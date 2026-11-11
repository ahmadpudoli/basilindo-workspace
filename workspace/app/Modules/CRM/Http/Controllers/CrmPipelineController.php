<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Domain\Models\CrmClosingStatus;
use App\Modules\CRM\Domain\Models\CrmAccount;
use App\Modules\CRM\Domain\Models\CrmTeam;
use App\Modules\CRM\Domain\Models\CrmOpportunity;
use App\Modules\CRM\Domain\Models\CrmPipelineStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CrmPipelineController extends Controller
{
    public function settings(Request $request): View
    {
        $this->authorizeStageConfiguration($request);
        return view('modules.crm.pipeline-settings', ['stages' => CrmPipelineStage::query()->orderBy('sequence')->get(), 'closingStatuses' => CrmClosingStatus::query()->orderBy('sequence')->get()]);
    }

    public function teams(Request $request): View
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('manage_crm_team'), 403);
        return view('modules.crm.teams', ['teams' => CrmTeam::query()->orderBy('name')->get()]);
    }

    public function storeTeam(Request $request)
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('manage_crm_team'), 403);
        CrmTeam::create($request->validate(['name' => ['required', 'string', 'max:150'], 'company_id' => ['nullable', 'integer', 'exists:core_companies,id'], 'leader_user_id' => ['nullable', 'integer', 'exists:core_users,id']]));
        return back()->with('status', 'Team Sales berhasil ditambahkan.');
    }

    public function storeStage(Request $request)
    {
        $this->authorizeStageConfiguration($request);
        CrmPipelineStage::create($request->validate(['code' => ['required', 'alpha_dash', 'max:80', 'unique:crm.pipeline_stages,code'], 'name' => ['required', 'string', 'max:100'], 'sequence' => ['required', 'integer', 'min:0'], 'is_won' => ['nullable', 'boolean'], 'is_delivery' => ['nullable', 'boolean'], 'is_folded' => ['nullable', 'boolean'], 'requires_expected_close' => ['nullable', 'boolean']]));
        return back()->with('status', 'Stage pipeline berhasil ditambahkan.');
    }

    public function updateStage(Request $request, CrmPipelineStage $stage)
    {
        $this->authorizeStageConfiguration($request);
        $stage->update($request->validate(['name' => ['required', 'string', 'max:100'], 'sequence' => ['required', 'integer', 'min:0'], 'is_won' => ['nullable', 'boolean'], 'is_delivery' => ['nullable', 'boolean'], 'is_folded' => ['nullable', 'boolean'], 'requires_expected_close' => ['nullable', 'boolean'], 'is_active' => ['nullable', 'boolean']]));
        return back()->with('status', 'Stage pipeline berhasil diperbarui.');
    }

    public function storeClosingStatus(Request $request)
    {
        $this->authorizeStageConfiguration($request);
        CrmClosingStatus::create($request->validate(['code' => ['required', 'alpha_dash', 'max:50', 'unique:crm.closing_statuses,code'], 'name' => ['required', 'string', 'max:100'], 'sequence' => ['required', 'integer', 'min:0'], 'is_lost' => ['nullable', 'boolean'], 'is_ignored' => ['nullable', 'boolean'], 'is_closed' => ['nullable', 'boolean']]));
        return back()->with('status', 'Status closing berhasil ditambahkan.');
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CrmOpportunity::class);
        $showArchived = $request->boolean('show_archived');
        $companyIds = $request->user()->companies()->pluck('companies.id');
        $stages = CrmPipelineStage::query()->where('is_active', true)->orderBy('sequence')->get();
        $query = CrmOpportunity::query()->with(['account.company', 'company', 'contact', 'pipelineStage', 'closingStatus'])
            ->when($showArchived, fn ($query) => $query->withTrashed())
            ->when(! $showArchived, fn ($query) => $query->whereNull('deleted_at'))
            ->when(! $request->user()->hasRole('super_admin'), fn ($query) => $query->where(function ($scope) use ($companyIds): void {
                $scope->whereIn('company_id', $companyIds)->orWhereHas('account', fn ($account) => $account->whereIn('company_id', $companyIds));
            }))
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'ilike', '%'.trim($request->string('q')).'%'))
            ->get();

        $cards = $query->groupBy(fn (CrmOpportunity $opportunity) => $opportunity->stage_id ?: 'unassigned');
        $accounts = CrmAccount::query()->when(! $request->user()->hasRole('super_admin'), fn ($query) => $query->whereIn('company_id', $companyIds))->orderBy('name')->get(['id', 'name', 'company_id']);
        $teams = CrmTeam::query()->where('is_active', true)->orderBy('name')->get();
        return view('modules.crm.pipeline', compact('stages', 'cards', 'accounts', 'teams', 'showArchived'));
    }

    public function move(Request $request, CrmOpportunity $opportunity): JsonResponse
    {
        Gate::authorize('update', $opportunity);
        $data = $request->validate([
            'stage_id' => ['required', 'uuid', Rule::exists('crm.pipeline_stages', 'id')],
            'closing_status_id' => ['nullable', 'uuid', Rule::exists('crm.closing_statuses', 'id')],
            'lost_reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $stage = CrmPipelineStage::query()->where('is_active', true)->findOrFail($data['stage_id']);
        $closingStatus = ! empty($data['closing_status_id']) ? CrmClosingStatus::query()->where('is_active', true)->findOrFail($data['closing_status_id']) : null;
        if ($closingStatus?->is_lost || $closingStatus?->is_ignored) {
            if (blank($data['lost_reason'] ?? null)) throw ValidationException::withMessages(['lost_reason' => 'Alasan wajib diisi untuk status Lost atau Diabaikan.']);
        }
        DB::connection('crm')->transaction(function () use ($opportunity, $stage, $closingStatus, $data): void {
            $opportunity->forceFill([
                'stage_id' => $stage->getKey(), 'stage' => $stage->code,
                'closing_status_id' => $closingStatus?->getKey() ?: $opportunity->closing_status_id,
                'lost_reason' => $data['lost_reason'] ?? $opportunity->lost_reason,
                'won_at' => $stage->is_won && ! $opportunity->won_at ? now() : $opportunity->won_at,
                'closed_at' => $closingStatus?->is_closed && ! $opportunity->closed_at ? now() : $opportunity->closed_at,
            ])->save();
        });
        $fresh = $opportunity->fresh('closingStatus');
        return response()->json(['ok' => true, 'stage' => $stage->name, 'closing_status' => $fresh->closingStatus?->name]);
    }

    private function authorizeStageConfiguration(Request $request): void
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('manage_crm_pipeline'), 403);
    }
}

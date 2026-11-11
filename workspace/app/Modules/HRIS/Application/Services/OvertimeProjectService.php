<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Employee;
use Core\Models\Project;
use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class OvertimeProjectService
{
    public function available(Employee $employee, User $user): Builder
    {
        $query = Project::query();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if (! $user->can('view_project')) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereIn('company_id', $user->companies()->select('companies.id'))
            ->whereHas('members', fn (Builder $members) => $members->whereKey($user->getKey()));
    }

    public function resolve(Employee $employee, array $data, int $actorId): ?int
    {
        if (($data['overtime_type'] ?? 'internal') !== 'external_project' || blank($data['project_id'] ?? null)) {
            return null;
        }

        $user = User::findOrFail($actorId);
        $project = $this->available($employee, $user)->whereKey($data['project_id'])->first();

        if (! $project) {
            throw ValidationException::withMessages(['project_id' => 'Project tidak tersedia atau tidak dapat Anda akses.']);
        }

        return (int) $project->getKey();
    }
}

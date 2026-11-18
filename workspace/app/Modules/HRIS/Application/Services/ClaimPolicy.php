<?php
namespace App\Modules\HRIS\Application\Services;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Core\Models\User;
class ClaimPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('view_any_claim');
    }

    public function view(User $user, Claim $claim): bool
    {
        if ($claim->status === 'draft' && (int) $claim->created_by === (int) $user->getKey()) {
            return true;
        }

        return (int) $claim->requester_user_id === (int) $user->getKey()
            || (int) $claim->employee?->user_id === (int) $user->getKey();
    }

    public function viewFromSummary(User $user, Claim $claim): bool
    {
        if ($claim->status === 'draft') {
            return false;
        }
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $inCompanyScope = $user->companies()->whereKey($claim->company_id)->exists();
        if (! $inCompanyScope) {
            return false;
        }

        $summaryPermission = $claim->type === 'reimbursement'
            ? 'view_hris_reimbursement_summary'
            : 'view_hris_cash_advance_summary';

        return $this->viewAny($user)
            || ($claim->status !== 'draft' && $user->can($summaryPermission));
    }

    public function print(User $user, Claim $claim): bool
    {
        if ($this->view($user, $claim)) {
            return true;
        }

        if ($claim->status === 'draft') {
            return false;
        }

        if (! $user->companies()->whereKey($claim->company_id)->exists()) {
            return false;
        }

        $permission = match ($claim->type) {
            'reimbursement' => 'print_hris_reimbursement',
            'cash_advance' => 'print_hris_cash_advance',
            default => null,
        };

        return $permission !== null && $user->can($permission);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('create_claim');
    }

    public function update(User $user, Claim $claim): bool
    {
        return in_array($claim->status, ['draft', 'needs_revision'], true)
            && ((int) $claim->created_by === (int) $user->getKey() || (int) $claim->requester_user_id === (int) $user->getKey() || $user->hasRole('super_admin'));
    }

    public function viewAuditTrail(User $user, Claim $claim): bool
    {
        if ($user->hasRole('super_admin')) return true;
        if (! $user->can('view_claim_audit_trail')) return false;

        return $this->view($user, $claim) || $this->isCurrentApprovalSubject($user, $claim);
    }

    private function isCurrentApprovalSubject(User $user, Claim $claim): bool
    {
        if (! $user->companies()->whereKey($claim->company_id)->exists()) return false;

        return match ($claim->status) {
            'pending_manager' => (int) $claim->employee?->manager?->user_id === (int) $user->getKey(),
            'pending_finance' => in_array((int) $user->getKey(), HrisWorkflowApprover::approverIds((int) $claim->company_id, 'claim', 'finance'), true),
            'pending_final' => in_array((int) $user->getKey(), HrisWorkflowApprover::approverIds((int) $claim->company_id, 'claim', 'final'), true),
            default => false,
        };
    }
}

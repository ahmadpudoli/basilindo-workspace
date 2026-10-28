<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\ApprovalEvent;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use App\Modules\ProjectManagement\Domain\Models\Notification;
use Core\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HrisWorkflowService
{
    public function notify(int $userId, string $title, string $message, array $data = []): void
    {
        if (! User::query()->whereKey($userId)->exists()) return;
        Notification::create(['user_id' => $userId, 'type' => 'hris', 'title' => $title, 'message' => $message, 'data' => $data]);
        SendWebPushNotification::dispatch($userId, $title, $message, $data);
    }

    public function decide(object $request, string $requestType, string $step, string $action, User $actor, ?string $comment = null, ?string $revisionType = null): object
    {
        $allowed = ['approve', 'reject', 'request_revision'];
        if (! in_array($action, $allowed, true)) throw ValidationException::withMessages(['action' => 'Aksi approval tidak valid.']);
        if ($action === 'request_revision' && ($requestType !== 'leave' || blank($comment))) throw ValidationException::withMessages(['comment' => 'Alasan revisi wajib diisi.']);
        if ($action === 'request_revision' && ! in_array($revisionType, ['substantive', 'administrative'], true)) throw ValidationException::withMessages(['revision_type' => 'Jenis revisi wajib dipilih.']);
        return DB::connection('hris')->transaction(function () use ($request, $requestType, $step, $action, $actor, $comment, $revisionType): object {
            $statusMap = [
                'leave_manager' => ['approve' => 'pending_hr', 'reject' => 'rejected'],
                'leave_hr' => ['approve' => 'approved', 'reject' => 'rejected'],
                'overtime_manager' => ['approve' => 'pending_hr', 'reject' => 'rejected'],
                'overtime_hr' => ['approve' => 'approved', 'reject' => 'rejected'],
                'claim_manager' => ['approve' => 'pending_finance', 'reject' => 'rejected'],
                'claim_finance' => ['approve' => 'approved', 'reject' => 'rejected'],
                'claim_final' => ['approve' => 'approved', 'reject' => 'rejected'],
            ];
            $key = $requestType.'_'.$step;
            if (! isset($statusMap[$key])) throw ValidationException::withMessages(['step' => 'Tahap workflow tidak valid.']);
            if ($requestType === 'leave' && (($step === 'manager' && $request->status !== 'pending_manager') || ($step === 'hr' && $request->status !== 'pending_hr'))) throw ValidationException::withMessages(['step' => 'Pengajuan tidak berada pada tahap approval yang sesuai.']);
            $workflowKey = $requestType === 'leave' ? 'leave_sick' : $requestType;
            $configuredStep = $requestType === 'claim' && $step === 'finance' ? 'finance' : $step;
            if (in_array($configuredStep, ['hr', 'finance'], true) && ! in_array((int) $actor->getKey(), HrisWorkflowApprover::approverIds((int) $request->company_id, $workflowKey, $configuredStep), true)) {
                throw ValidationException::withMessages(['step' => 'Anda bukan approver yang ditetapkan untuk perusahaan ini.']);
            }
            if ($action === 'request_revision') {
                $request->forceFill(['status' => 'needs_revision', 'revision_requested_by' => $actor->getKey(), 'revision_requested_role' => $step, 'revision_type' => $revisionType, 'revision_reason' => $comment, 'revision_requested_at' => now(), 'decision_reason' => $comment])->save();
                ApprovalEvent::create(['request_type' => $requestType, 'request_id' => $request->getKey(), 'step' => $step, 'action' => 'request_revision', 'actor_id' => $actor->getKey(), 'comment' => $comment]);
                if ($request->requester_user_id) {
                    $requestLabel = $this->requestLabel($requestType, $request);
                    $actorName = $actor->name ?: $actor->email;
                    $stepLabel = $this->stepLabel($step);
                    $this->notify((int) $request->requester_user_id, "{$requestLabel} perlu direvisi", "{$requestLabel} diminta revisi oleh {$actorName} sebagai {$stepLabel}. Catatan: {$comment}", ['request_type' => $requestType, 'request_id' => $request->getKey(), 'status' => 'needs_revision', 'action' => 'request_revision', 'actor_id' => $actor->getKey(), 'actor_name' => $actorName, 'step' => $step]);
                }
                return $request;
            }
            $nextStatus = $statusMap[$key][$action];
            if ($requestType === 'claim' && $step === 'finance' && $action === 'approve') {
                $nextStatus = $request->type === 'cash_advance' ? 'awaiting_accountability' : (app(ClaimService::class)->requiresFinalApproval($request) ? 'pending_final' : 'approved');
            }
            $request->status = $nextStatus;
            $field = $step.'_approved_by'; $time = $step.'_approved_at';
            if ($action === 'approve') $request->forceFill([$field => $actor->getKey(), $time => now()]);
            $request->forceFill(['decision_reason' => $comment]);
            $request->save();
            ApprovalEvent::create(['request_type' => $requestType, 'request_id' => $request->getKey(), 'step' => $step, 'action' => $action, 'actor_id' => $actor->getKey(), 'comment' => $comment]);
            $requestLabel = $this->requestLabel($requestType, $request);
            $actorName = $actor->name ?: $actor->email;
            $stepLabel = $this->stepLabel($step);
            $decisionLabel = $action === 'approve' ? 'disetujui' : 'ditolak';
            $decisionMessage = "{$requestLabel} {$decisionLabel} oleh {$actorName} sebagai {$stepLabel}.";
            if (filled($comment)) $decisionMessage .= " Catatan: {$comment}";
            if ($request->employee?->user_id) $this->notify($request->employee->user_id, "{$requestLabel} {$decisionLabel}", $decisionMessage, ['request_type' => $requestType, 'request_id' => $request->getKey(), 'status' => $request->status, 'action' => $action, 'actor_id' => $actor->getKey(), 'actor_name' => $actorName, 'step' => $step]);
            if ($action === 'approve' && $step === 'manager' && in_array($request->status, ['pending_hr', 'pending_finance'], true)) {
                $configuredStep = $request->status === 'pending_finance' ? 'finance' : 'hr';
                $nextStepLabel = $configuredStep === 'finance' ? 'Finance' : 'HR';
                foreach (HrisWorkflowApprover::approverIds((int) $request->company_id, $workflowKey, $configuredStep) as $approverId) {
                    $this->notify($approverId, "{$requestLabel} menunggu approval {$nextStepLabel}", "{$requestLabel} disetujui oleh {$actorName} sebagai Atasan dan sekarang menunggu persetujuan {$nextStepLabel}.", ['request_type' => $requestType, 'request_id' => $request->getKey(), 'status' => $request->status, 'action' => 'pending_approval', 'approval' => true, 'actor_id' => $actor->getKey(), 'actor_name' => $actorName, 'step' => $configuredStep]);
                }
            }
            return $request;
        });
    }

    private function requestLabel(string $requestType, object $request): string
    {
        return match ($requestType) {
            'leave' => $request->type === 'sick' ? 'Pengajuan sakit' : 'Pengajuan cuti',
            'overtime' => 'Pengajuan lembur',
            'claim' => $request->type === 'cash_advance' ? 'Pengajuan cash advance' : 'Pengajuan reimbursement',
            default => 'Pengajuan HRIS',
        };
    }

    private function stepLabel(string $step): string
    {
        return match ($step) {
            'manager' => 'Atasan',
            'hr' => 'HR',
            'finance' => 'Finance',
            'final' => 'Approver final',
            default => ucfirst($step),
        };
    }
}

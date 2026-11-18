<?php

namespace App\Modules\Core\Notifications\Services;

use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;

class NotificationLinkResolver
{
    public function url(array $data): ?string
    {
        $requestType = $data['request_type'] ?? null;
        $requestId = $data['request_id'] ?? null;
        $isApproval = ($data['approval'] ?? false) === true || ($data['action'] ?? null) === 'pending_approval';

        if ($isApproval) {
            if (! is_string($requestType) || blank($requestId)) {
                return null;
            }

            $step = (string) ($data['step'] ?? 'manager');
            $request = $this->findRequest($requestType, $requestId);
            $expectedStatus = match ($step) {
                'manager' => 'pending_manager',
                'finance' => 'pending_finance',
                'final' => 'pending_final',
                default => 'pending_hr',
            };

            return $request?->status === $expectedStatus
                ? route('workspace.hris.approval-detail', [$requestType, $requestId, $step])
                : route('workspace.hris.approvals');
        }

        if (! is_string($requestType) || blank($requestId)) {
            return filled($data['document_id'] ?? null)
                ? route('documents.preview', $data['document_id'])
                : null;
        }

        return match ($requestType) {
            'leave' => route('workspace.hris.leave-requests.show', $requestId),
            'overtime' => route('workspace.hris.overtime.show', $requestId),
            'claim' => route('workspace.hris.claims.show', $requestId),
            default => filled($data['document_id'] ?? null)
                ? route('documents.preview', $data['document_id'])
                : null,
        };
    }

    public function label(array $data): string
    {
        if (($data['approval'] ?? false) === true || ($data['action'] ?? null) === 'pending_approval') {
            return $this->url($data) === route('workspace.hris.approvals')
                ? 'Buka daftar persetujuan'
                : 'Buka detail persetujuan';
        }

        return match ($data['request_type'] ?? null) {
            'leave' => 'Lihat detail pengajuan cuti/sakit',
            'overtime' => 'Lihat detail pengajuan lembur',
            'claim' => 'Lihat detail pengajuan',
            default => filled($data['document_id'] ?? null) ? 'Lihat dokumen terkait' : 'Buka terkait',
        };
    }

    private function findRequest(string $requestType, mixed $requestId): LeaveRequest|OvertimeRequest|Claim|null
    {
        return match ($requestType) {
            'leave' => LeaveRequest::query()->find($requestId),
            'overtime' => OvertimeRequest::query()->find($requestId),
            'claim' => Claim::query()->find($requestId),
            default => null,
        };
    }
}

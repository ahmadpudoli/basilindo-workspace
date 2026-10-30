<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\ProjectManagement\Domain\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $read = $request->string('read')->toString();
        $type = $request->string('type')->toString();
        $query = Notification::query()
            ->where('user_id', $request->user()->getKey())
            ->when($request->filled('q'), fn ($query) => $query->where(function ($nested) use ($request): void {
                $value = '%'.$request->string('q')->toString().'%';
                $nested->where('title', 'ilike', $value)->orWhere('message', 'ilike', $value);
            }))
            ->when($read === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->when($read === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->latest();

        $notifications = $query->paginate($perPage)->withQueryString();
        $notifications->getCollection()->transform(fn (Notification $notification): Notification => $this->hydrateHrisIdentity($notification));

        return view('modules.core.notifications', [
            'notifications' => $notifications,
            'perPage' => $perPage,
            'read' => $read,
            'type' => $type,
        ]);
    }

    public function show(Request $request, Notification $notification): View|RedirectResponse
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->getKey(), 403);
        $notification = $this->hydrateHrisIdentity($notification);
        $notification->markAsRead();
        $targetUrl = $this->targetUrl($notification);

        if ((($notification->data['approval'] ?? false) === true || ($notification->data['action'] ?? null) === 'pending_approval') && $targetUrl) {
            return redirect()->to($targetUrl);
        }

        return view('modules.core.notification-show', [
            'notification' => $notification,
            'targetUrl' => $targetUrl,
            'targetLabel' => $this->targetLabel($notification),
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        Notification::query()->where('user_id', $request->user()->getKey())->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    public function targetUrl(Notification $notification): ?string
    {
        $data = $notification->data ?? [];
        $requestType = $data['request_type'] ?? null;
        $requestId = $data['request_id'] ?? null;

        $isApprovalNotification = ($data['approval'] ?? false) === true || ($data['action'] ?? null) === 'pending_approval';
        if ($isApprovalNotification && filled($requestType) && filled($requestId)) {
            $step = $data['step'] ?? 'manager';
            $request = match ($requestType) {
                'leave' => LeaveRequest::query()->find($requestId),
                'overtime' => OvertimeRequest::query()->find($requestId),
                'claim' => Claim::query()->find($requestId),
                default => null,
            };
            $expectedStatus = $step === 'manager' ? 'pending_manager' : ($step === 'finance' ? 'pending_finance' : 'pending_hr');
            if ($request && $request->status === $expectedStatus) return route('workspace.hris.approval-detail', [$requestType, $requestId, $step]);

            return route('workspace.hris.approvals');
        }
        if ($requestType === 'leave' && filled($requestId)) return route('workspace.hris.leave-requests.show', $requestId);
        if ($requestType === 'overtime') return route('workspace.hris.overtime');
        if ($requestType === 'claim') return route('workspace.hris.claims');
        if (filled($data['document_id'] ?? null)) return route('documents.preview', $data['document_id']);

        return null;
    }

    private function targetLabel(Notification $notification): string
    {
        if (($notification->data['approval'] ?? false) === true || ($notification->data['action'] ?? null) === 'pending_approval') {
            $requestType = $notification->data['request_type'] ?? null;
            $requestId = $notification->data['request_id'] ?? null;
            $step = $notification->data['step'] ?? 'manager';
            $request = match ($requestType) {
                'leave' => filled($requestId) ? LeaveRequest::query()->find($requestId) : null,
                'overtime' => filled($requestId) ? OvertimeRequest::query()->find($requestId) : null,
                'claim' => filled($requestId) ? Claim::query()->find($requestId) : null,
                default => null,
            };
            $expectedStatus = $step === 'manager' ? 'pending_manager' : ($step === 'finance' ? 'pending_finance' : 'pending_hr');
            return $request?->status === $expectedStatus ? 'Buka detail persetujuan' : 'Buka daftar persetujuan';
        }

        return match ($notification->data['request_type'] ?? null) {
            'leave' => 'Buka detail pengajuan cuti/sakit',
            'overtime' => 'Buka daftar lembur',
            'claim' => 'Buka daftar claim',
            default => filled($notification->data['document_id'] ?? null) ? 'Buka dokumen terkait' : 'Buka terkait',
        };
    }

    private function hydrateHrisIdentity(Notification $notification): Notification
    {
        $data = $notification->data ?? [];
        $requestType = $data['request_type'] ?? null;
        $requestId = $data['request_id'] ?? null;
        if (! is_string($requestType) || ! filled($requestId)) return $notification;

        $request = match ($requestType) {
            'leave' => LeaveRequest::query()->with('employee')->find($requestId),
            'overtime' => OvertimeRequest::query()->with('employee')->find($requestId),
            'claim' => Claim::query()->with('employee')->find($requestId),
            default => null,
        };
        if (! $request || blank($request->request_number)) return $notification;

        $data['request_number'] = $request->request_number;
        $data['requester_name'] = $request->employee?->full_name;
        $notification->data = $data;
        if (! str_contains((string) $notification->title, $request->request_number)) $notification->title .= ' ['.$request->request_number.']';
        if (! str_contains((string) $notification->message, $request->request_number)) $notification->message .= ' Nomor pengajuan: '.$request->request_number.'; pemohon: '.($request->employee?->full_name ?? '—').'.';

        return $notification;
    }
}

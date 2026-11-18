<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\Claim;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Domain\Models\OvertimeRequest;
use App\Modules\ProjectManagement\Domain\Models\Notification;
use App\Modules\Core\Notifications\Services\NotificationLinkResolver;
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
        $targetUrl = app(NotificationLinkResolver::class)->url($notification->data ?? []);

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

    public function emailPreferences(Request $request): RedirectResponse
    {
        return redirect()->route('workspace.account.settings', ['section' => 'notifications']);
    }

    public function updateEmailPreferences(Request $request): RedirectResponse
    {
        $categories = array_keys(config('notifications.email.preference_categories', []));
        $rules = [
            'email_notification_preferences' => ['required', 'array:'.implode(',', $categories)],
        ];
        foreach ($categories as $category) {
            $rules['email_notification_preferences.'.$category] = ['required', 'boolean'];
        }

        $data = $request->validate($rules);
        $preferences = [];
        foreach ($categories as $category) {
            $preferences[$category] = (bool) $data['email_notification_preferences'][$category];
        }

        $request->user()->forceFill([
            'email_notification_preferences' => $preferences,
        ])->save();

        return redirect()->route('workspace.account.settings', ['section' => 'notifications'])->with('status', 'Preferensi email notifikasi berhasil diperbarui.');
    }

    public function targetUrl(Notification $notification): ?string
    {
        return app(NotificationLinkResolver::class)->url($notification->data ?? []);
    }

    private function targetLabel(Notification $notification): string
    {
        return app(NotificationLinkResolver::class)->label($notification->data ?? []);
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
        $requestLabel = match ($requestType) {
            'leave' => $request->type === 'sick' ? 'Pengajuan sakit' : 'Pengajuan cuti',
            'overtime' => 'Pengajuan lembur',
            'claim' => $request->type === 'cash_advance' ? 'Pengajuan cash advance' : 'Pengajuan reimbursement',
            default => 'Pengajuan HRIS',
        };
        $requestLabel .= ' ['.$request->request_number.']';
        $stepLabel = match ($data['step'] ?? null) {
            'manager' => 'Atasan',
            'hr' => 'HR',
            'finance' => 'Finance',
            'final' => 'Approver final',
            default => null,
        };
        $notification->title = match ($data['action'] ?? null) {
            'approve' => $stepLabel ? "Disetujui oleh {$stepLabel}: {$requestLabel}" : $notification->title,
            'reject' => $stepLabel ? "Ditolak oleh {$stepLabel}: {$requestLabel}" : $notification->title,
            'request_revision' => $stepLabel ? "Revisi diminta oleh {$stepLabel}: {$requestLabel}" : $notification->title,
            'pending_approval' => match ($data['step'] ?? null) {
                'manager' => "Persetujuan Atasan diperlukan: {$requestLabel}",
                'hr' => "Pemeriksaan HR diperlukan: {$requestLabel}",
                'finance' => "Pemeriksaan Finance diperlukan: {$requestLabel}",
                'final' => "Persetujuan Approver final diperlukan: {$requestLabel}",
                default => $notification->title,
            },
            default => $notification->title,
        };
        if (! str_contains((string) $notification->title, $request->request_number)) $notification->title .= ' ['.$request->request_number.']';
        if (! str_contains((string) $notification->message, $request->request_number)) $notification->message .= ' Nomor pengajuan: '.$request->request_number.'; pemohon: '.($request->employee?->full_name ?? '—').'.';

        return $notification;
    }
}

<?php

namespace App\Modules\Core\Notifications\Services;

use App\Modules\Core\Notifications\Jobs\SendEmailNotification;
use App\Modules\Core\Notifications\Models\EmailNotificationLog;
use App\Modules\Core\Application\Services\PayloadCipher;
use App\Modules\ProjectManagement\Domain\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class EmailNotificationDispatcher
{
    public function dispatch(Notification $notification): void
    {
        $user = $notification->user;
        $data = $notification->data ?? [];
        if (! $user || blank($user->email)) return;

        $category = app(EmailNotificationCategoryResolver::class)->resolve($notification);
        $preferences = (array) ($user->email_notification_preferences ?? []);
        if (! ($preferences[$category] ?? true)) return;

        $linkResolver = app(NotificationLinkResolver::class);
        $targetUrl = $linkResolver->url($data);
        $template = (string) config('notifications.email.templates.'.$notification->type, config('notifications.email.default_template'));
        $body = View::make($template, [
            'notification' => $notification,
            'recipient' => $user,
            'targetUrl' => $targetUrl,
            'targetLabel' => $linkResolver->label($data),
        ])->render();
        $log = EmailNotificationLog::query()->firstOrCreate(
            ['notification_id' => $notification->getKey()],
            [
                'user_id' => $user->getKey(),
                'recipient' => $user->email,
                'template' => $template,
                'subject' => Str::limit((string) ($notification->title ?: 'Notifikasi Basilindo Workspace'), 180, ''),
                'encrypted_body' => app(PayloadCipher::class)->encrypt($body),
                'status' => 'queued',
                'queued_at' => now(),
            ],
        );

        if ($log->wasRecentlyCreated) SendEmailNotification::dispatch($log->getKey())->afterCommit();
    }
}

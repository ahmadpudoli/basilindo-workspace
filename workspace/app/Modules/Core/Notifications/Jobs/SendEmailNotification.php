<?php

namespace App\Modules\Core\Notifications\Jobs;

use App\Modules\Core\Notifications\Models\EmailNotificationLog;
use App\Modules\Core\Application\Services\PayloadCipher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const MAX_ATTEMPTS = 3;

    public int $tries = self::MAX_ATTEMPTS;
    public array $backoff = [30, 120, 300];
    public int $timeout = 60;

    public function __construct(public readonly int $emailLogId)
    {
        $this->onQueue((string) config('workers.groups.email.queues.0', 'email-notifications'));
    }

    public function handle(PayloadCipher $cipher): void
    {
        $log = EmailNotificationLog::query()->findOrFail($this->emailLogId);
        if ($log->status === 'sent') return;

        $log->update(['status' => 'processing', 'attempts' => $log->attempts + 1, 'error_message' => null]);
        $body = $cipher->decrypt($log->encrypted_body);
        Mail::html($body, function ($message) use ($log): void {
            $message->to($log->recipient)
                ->subject($log->subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
        $log->update(['status' => 'sent', 'sent_at' => now(), 'error_message' => null]);
    }

    public function failed(?Throwable $exception): void
    {
        EmailNotificationLog::query()->whereKey($this->emailLogId)->where('status', '!=', 'sent')->update([
            'status' => 'failed',
            'error_message' => $exception ? get_class($exception).': email delivery failed' : 'Email delivery failed',
        ]);
    }
}

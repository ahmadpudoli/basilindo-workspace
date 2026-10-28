<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendWebPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $message,
        public readonly array $data = [],
    ) {}

    public function handle(): void
    {
        $config = config('services.webpush');
        if (blank($config['public_key'] ?? null) || blank($config['private_key'] ?? null)) {
            return;
        }

        $subscriptions = WebPushSubscription::query()->where('user_id', $this->userId)->get();
        if ($subscriptions->isEmpty()) return;

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $config['subject'],
                'publicKey' => $config['public_key'],
                'privateKey' => $config['private_key'],
            ],
        ], ['TTL' => (int) ($config['ttl'] ?? 300), 'urgency' => 'high']);

        $payload = json_encode([
            'title' => $this->title,
            'body' => $this->message,
            'data' => $this->data,
            'url' => $this->data['url'] ?? url('/dashboard'),
        ], JSON_THROW_ON_ERROR);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(Subscription::create([
                'endpoint' => $subscription->endpoint,
                'keys' => ['p256dh' => $subscription->public_key, 'auth' => $subscription->auth_token],
                'contentEncoding' => $subscription->content_encoding,
            ]), $payload);
        }

        foreach ($webPush->flush() as $report) {
            $record = $subscriptions->first(fn (WebPushSubscription $item): bool => $item->endpoint === $report->getEndpoint());
            if (! $record) continue;
            if ($report->isSubscriptionExpired()) $record->delete();
            elseif ($report->isSuccess()) $record->update(['last_used_at' => now()]);
        }
    }
}

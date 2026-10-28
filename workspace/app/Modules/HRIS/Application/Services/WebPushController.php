<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebPushController
{
    public function config(): JsonResponse
    {
        return response()->json(['enabled' => filled(config('services.webpush.public_key')), 'publicKey' => config('services.webpush.public_key')]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2000'],
            'keys.p256dh' => ['required', 'string', 'max:500'],
            'keys.auth' => ['required', 'string', 'max:500'],
            'contentEncoding' => ['nullable', 'in:aes128gcm,aesgcm'],
        ]);

        WebPushSubscription::updateOrCreate(
            ['user_id' => $request->user()->getKey(), 'endpoint' => $data['endpoint']],
            [
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
                'content_encoding' => $data['contentEncoding'] ?? 'aes128gcm',
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'last_used_at' => now(),
            ],
        );

        return response()->json(['saved' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => ['required', 'url', 'max:2000']]);
        WebPushSubscription::query()->where('user_id', $request->user()->getKey())->where('endpoint', (string) $request->string('endpoint'))->delete();
        return response()->json(['deleted' => true]);
    }
}

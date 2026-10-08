<?php

namespace App\Services;

use Core\Models\ApplicationAccess;
use Core\Models\ExternalIdentity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SsoApplicationAccessSyncService
{
    public function sync(string $application): int
    {
        $clientId = (string) config('sso.access_sync_client_id');
        $clientSecret = (string) config('sso.access_sync_client_secret');
        abort_if($clientId === '' || $clientSecret === '', 503, 'SSO access sync credentials belum dikonfigurasi.');

        $issuer = rtrim((string) config('sso.issuer_url'), '/');
        $token = Http::asForm()->post($issuer.'/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'application:access:read',
        ])->throw()->json('access_token');

        $payload = Http::withToken($token)
            ->get($issuer.'/api/internal/applications/'.rawurlencode($application).'/users')
            ->throw()
            ->json();

        $records = collect($payload['data'] ?? [])->filter(
            fn ($record): bool => is_array($record) && isset($record['subject'], $record['status'])
        )->values();

        DB::connection('core')->transaction(function () use ($application, $issuer, $records): void {
            $subjects = $records->pluck('subject')->map(fn ($subject): string => (string) $subject)->all();

            $stale = ApplicationAccess::query()->where('application_code', $application);
            if ($subjects !== []) {
                $stale->whereNotIn('subject', $subjects);
            }
            $stale->update(['status' => 'revoked', 'synced_at' => now(), 'updated_at' => now()]);

            foreach ($records as $record) {
                $subject = (string) $record['subject'];
                $userId = ExternalIdentity::query()
                    ->where('issuer', $issuer)
                    ->where('subject', $subject)
                    ->value('user_id');

                ApplicationAccess::query()->updateOrCreate(
                    ['application_code' => $application, 'subject' => $subject],
                    ['user_id' => $userId, 'status' => (string) $record['status'], 'synced_at' => now()],
                );
            }
        });

        return $records->count();
    }
}

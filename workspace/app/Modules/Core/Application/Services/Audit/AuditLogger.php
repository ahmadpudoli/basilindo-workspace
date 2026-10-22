<?php

namespace App\Modules\Core\Application\Services\Audit;

use App\Modules\DocumentManagement\Domain\Models\AuditEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuditLogger
{
    public function record(string $action, ?Model $subject = null, ?int $companyId = null, array $metadata = [], ?array $oldValues = null, ?array $newValues = null): AuditEvent
    {
        $request = Request::instance();
        $userAgent = Str::limit((string) $request->userAgent(), 1000, '');
        $attributes = [
            'company_id' => $companyId ?? $this->companyId($subject),
            'actor_id' => Auth::id(),
            'action' => $action,
            'module' => Str::before($action, '.') ?: 'system',
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'request_id' => $request->header('X-Request-ID') ?: $request->header('X-Correlation-ID'),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent ?: null,
            'device' => $this->device($userAgent),
            'http_method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'metadata' => $metadata,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'created_at' => now(),
        ];

        // Permission seeders can run before the final document table prefix
        // migration. Keep bootstrapping idempotent without losing runtime audit
        // events once the audit table is available.
        if (! Schema::connection('documents')->hasTable('audit_events')) {
            return new AuditEvent($attributes);
        }

        return AuditEvent::create($attributes);
    }

    public function recordChange(string $action, Model $subject, array $oldValues, array $newValues, ?int $companyId = null, array $metadata = []): AuditEvent
    {
        return $this->record($action, $subject, $companyId, $metadata, $oldValues, $newValues);
    }

    private function companyId(?Model $subject): ?int
    {
        if (! $subject) return null;
        return $subject->company_id ?? $subject->company?->getKey();
    }

    private function device(?string $userAgent): ?string
    {
        if (blank($userAgent)) return null;

        $browser = match (true) {
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome') => 'Chrome',
            str_contains($userAgent, 'Safari') => 'Safari',
            default => 'Browser',
        };
        $os = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };

        return $browser.' / '.$os;
    }
}

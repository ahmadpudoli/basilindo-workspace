<?php

namespace App\Services\Audit;

use App\Models\AuditEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function record(string $action, ?Model $subject = null, ?int $companyId = null, array $metadata = []): AuditEvent
    {
        return AuditEvent::create([
            'company_id' => $companyId ?? $this->companyId($subject),
            'actor_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'request_id' => Request::header('X-Request-ID') ?: Request::header('X-Correlation-ID'),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    private function companyId(?Model $subject): ?int
    {
        if (! $subject) return null;
        return $subject->company_id ?? $subject->company?->getKey();
    }
}

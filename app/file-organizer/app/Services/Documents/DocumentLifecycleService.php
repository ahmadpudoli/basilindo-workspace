<?php

namespace App\Services\Documents;

use App\Models\Document;
use Illuminate\Validation\ValidationException;

class DocumentLifecycleService
{
    public function release(Document $document, int $actorId): Document
    {
        if ($document->status !== 'quarantine') {
            throw ValidationException::withMessages(['status' => 'Hanya dokumen quarantine yang dapat dirilis.']);
        }

        $document->forceFill([
            'status' => 'ready',
            'released_at' => now(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ])->save();

        app(\App\Services\Audit\AuditLogger::class)->record('document.released', $document, $document->company_id, [
            'actor_id' => $actorId,
        ]);

        return $document->refresh();
    }

    public function reject(Document $document, int $actorId, string $reason): Document
    {
        $reason = trim($reason);
        if ($document->status !== 'quarantine') {
            throw ValidationException::withMessages(['status' => 'Hanya dokumen quarantine yang dapat ditolak.']);
        }
        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => 'Alasan penolakan wajib diisi.']);
        }

        $document->forceFill([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'released_at' => null,
        ])->save();

        app(\App\Services\Audit\AuditLogger::class)->record('document.rejected', $document, $document->company_id, [
            'actor_id' => $actorId,
            'reason_length' => mb_strlen($reason),
        ]);

        return $document->refresh();
    }

    public function setLegalHold(Document $document, int $actorId, bool $enabled): Document
    {
        $document->forceFill(['legal_hold' => $enabled])->save();
        app(\App\Services\Audit\AuditLogger::class)->record('document.legal_hold_changed', $document, $document->company_id, [
            'actor_id' => $actorId,
            'enabled' => $enabled,
        ]);

        return $document->refresh();
    }
}

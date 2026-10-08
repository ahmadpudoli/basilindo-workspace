<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentRelation;
use Core\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DocumentRelationService
{
    public function create(Document $source, Document $target, string $type, User $user): DocumentRelation
    {
        if ($source->is($target) || $source->company_id !== $target->company_id) {
            throw new InvalidArgumentException('Dokumen harus berbeda dan berasal dari perusahaan yang sama.');
        }
        if (! $user->companies()->whereKey($source->company_id)->exists() && ! $user->hasRole('super_admin')) {
            throw new AuthorizationException('User tidak memiliki akses ke perusahaan dokumen.');
        }

        return DB::transaction(function () use ($source, $target, $type, $user) {
            $relation = DocumentRelation::firstOrCreate([
                'source_document_id' => $source->id,
                'target_document_id' => $target->id,
                'relation_type' => $type,
            ], ['created_by' => $user->id]);
            app(\App\Services\Audit\AuditLogger::class)->record('document.related', $source, $source->company_id, [
                'target_document_id' => $target->id, 'relation_type' => $type,
            ]);
            return $relation;
        });
    }
}

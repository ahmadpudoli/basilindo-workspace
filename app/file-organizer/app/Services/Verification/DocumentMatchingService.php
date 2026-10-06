<?php

namespace App\Services\Verification;

use App\Models\Document;
use App\Models\VerificationCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DocumentMatchingService
{
    /**
     * @param  array<int, Document>  $documents
     */
    public function createCase(array $documents, int $userId): VerificationCase
    {
        $documents = Collection::make($documents)->values();

        if ($documents->count() < 2) {
            throw new InvalidArgumentException('Minimal dua dokumen diperlukan untuk matching.');
        }

        if ($documents->pluck('company_id')->unique()->count() !== 1) {
            throw new InvalidArgumentException('Dokumen dari perusahaan berbeda tidak dapat dicocokkan.');
        }

        $referenceValues = $documents->pluck('normalized_reference_number')->filter()->unique();
        $amountValues = $documents->pluck('amount')->filter()->map(fn ($amount) => (string) $amount)->unique();
        $referencePassed = $referenceValues->count() === 1 && $documents->every(fn (Document $document) => filled($document->normalized_reference_number));
        $amountPassed = $amountValues->count() === 1 && $documents->every(fn (Document $document) => filled($document->amount));
        $allPassed = $referencePassed && $amountPassed;

        return DB::transaction(function () use ($documents, $userId, $referencePassed, $amountPassed, $allPassed, $referenceValues, $amountValues) {
            $case = VerificationCase::create([
                'company_id' => $documents->first()->company_id,
                'status' => $allPassed ? 'approved' : 'open',
                'created_by' => $userId,
                'summary' => $allPassed ? 'Semua rule matching dasar terpenuhi.' : 'Ditemukan discrepancy pada matching dasar.',
                'resolved_at' => $allPassed ? now() : null,
            ]);

            foreach ($documents as $document) {
                $case->items()->createMany([
                    [
                        'document_id' => $document->id,
                        'rule_code' => 'reference_number_match',
                        'result' => $referencePassed ? 'passed' : 'failed',
                        'evidence' => ['values' => $referenceValues->values()->all()],
                        'discrepancy' => $referencePassed ? null : 'Nomor referensi tidak sama atau belum lengkap.',
                    ],
                    [
                        'document_id' => $document->id,
                        'rule_code' => 'amount_match',
                        'result' => $amountPassed ? 'passed' : 'failed',
                        'evidence' => ['values' => $amountValues->values()->all()],
                        'discrepancy' => $amountPassed ? null : 'Nominal tidak sama atau belum lengkap.',
                    ],
                ]);
            }

            return $case->load('items');
        });
    }
}

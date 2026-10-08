<?php

namespace App\Services\Verification;

use Core\Models\User;
use App\Models\VerificationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

class VerificationReviewService
{
    public function review(VerificationItem $item, User $reviewer, string $result, ?string $comment = null): VerificationItem
    {
        if (! in_array($result, ['passed', 'failed', 'waived'], true)) {
            throw new InvalidArgumentException('Hasil review tidak valid.');
        }

        if (! $reviewer->companies()->whereKey($item->verificationCase->company_id)->exists()) {
            throw new AuthorizationException('Reviewer tidak memiliki akses ke perusahaan dokumen.');
        }

        return DB::transaction(function () use ($item, $reviewer, $result, $comment) {
            $item->update([
                'result' => $result,
                'reviewer_comment' => $comment,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $case = $item->verificationCase()->with('items')->first();
            $allReviewed = $case->items->every(fn ($reviewItem) => filled($reviewItem->reviewed_at));
            $hasFailure = $case->items->contains(fn ($reviewItem) => $reviewItem->result === 'failed');
            $case->update(['status' => $hasFailure ? 'rejected' : ($allReviewed ? 'approved' : 'in_review'), 'resolved_at' => $allReviewed ? now() : null]);
            app(\App\Services\Audit\AuditLogger::class)->record('verification.reviewed', $item, $case->company_id, [
                'result' => $result, 'verification_case_id' => $case->id,
            ]);

            return $item->fresh();
        });
    }
}

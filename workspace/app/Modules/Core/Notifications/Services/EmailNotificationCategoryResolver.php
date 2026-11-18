<?php

namespace App\Modules\Core\Notifications\Services;

use App\Modules\ProjectManagement\Domain\Models\Notification;

class EmailNotificationCategoryResolver
{
    public function resolve(Notification $notification): string
    {
        $categories = config('notifications.email.preference_categories', []);
        $data = $notification->data ?? [];
        $explicitCategory = $data['email_category'] ?? null;

        if (is_string($explicitCategory) && array_key_exists($explicitCategory, $categories)) {
            return $explicitCategory;
        }

        if ($notification->type !== 'hris') {
            return 'other';
        }

        return match ($data['request_type'] ?? null) {
            'leave' => 'leave_sick',
            'overtime' => 'overtime',
            'claim' => match ($data['claim_type'] ?? $data['request_subtype'] ?? null) {
                'reimbursement' => 'reimbursement',
                'cash_advance' => 'cash_advance',
                default => 'other',
            },
            default => 'other',
        };
    }
}

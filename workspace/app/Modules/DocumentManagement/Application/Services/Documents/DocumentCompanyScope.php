<?php

namespace App\Modules\DocumentManagement\Application\Services\Documents;

use Core\Models\User;

class DocumentCompanyScope
{
    /**
     * @return array<int, int>|null
     */
    public function ids(User $user): ?array
    {
        if ($user->hasRole('super_admin')) {
            return null;
        }

        $ids = $user->companies()
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        if ($ids !== []) {
            return $ids;
        }

        return [];
    }

    public function canAccess(User $user, int $companyId): bool
    {
        $ids = $this->ids($user);

        return $ids === null || in_array($companyId, $ids, true);
    }
}

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
        $ids = $user->companies()
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        if ($ids !== []) {
            return $ids;
        }

        return $user->hasRole('super_admin') ? null : [];
    }

    public function canAccess(User $user, int $companyId): bool
    {
        $ids = $this->ids($user);

        return $ids === null || in_array($companyId, $ids, true);
    }
}

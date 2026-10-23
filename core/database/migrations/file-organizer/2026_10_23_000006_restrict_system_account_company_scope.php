<?php

use Core\Models\Company;
use Core\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $groupId = Company::query()->where('code', 'BASILINDO')->value('id');
        if (! $groupId) {
            return;
        }

        $allowedCompanyIds = Company::query()
            ->where('status', 'active')
            ->where('classification', 'internal')
            ->where(function ($query) use ($groupId): void {
                $query->where('code', 'BASILINDO')
                    ->orWhere('parent_company_id', $groupId);
            })
            ->pluck('id')
            ->mapWithKeys(fn (int $id): array => [$id => ['scope_role' => 'member']])
            ->all();

        User::query()
            ->where('is_system_account', true)
            ->each(fn (User $user) => $user->companies()->sync($allowedCompanyIds));
    }

    public function down(): void
    {
        // Scope correction is intentionally not reversed: restoring unauthorized
        // company access would violate the system-account security invariant.
    }
};

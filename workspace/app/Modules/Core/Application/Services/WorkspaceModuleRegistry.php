<?php

namespace App\Modules\Core\Application\Services;

use Core\Models\Setting;

class WorkspaceModuleRegistry
{
    public const DEFAULT_MODULES = ['company', 'crm', 'project-management', 'documents', 'hris'];

    public function enabledKeys(): array
    {
        $raw = Setting::query()->where('key', 'workspace.active_modules')->whereNull('user_id')->value('value');
        $keys = $raw ? json_decode($raw, true) : self::DEFAULT_MODULES;

        return array_values(array_intersect(is_array($keys) ? $keys : self::DEFAULT_MODULES, self::DEFAULT_MODULES));
    }

    public function isEnabled(string $key): bool
    {
        return in_array($key, $this->enabledKeys(), true);
    }

    public function saveEnabledKeys(array $keys): void
    {
        Setting::setValue('workspace.active_modules', array_values(array_intersect($keys, self::DEFAULT_MODULES)), 'workspace');
    }
}

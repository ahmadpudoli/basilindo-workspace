<?php

namespace App\Filament\Support;

trait HasWorkspaceModuleNavigation
{
    public static function shouldRegisterNavigation(): bool
    {
        return session('workspace.active_module') === static::workspaceModule();
    }

    public static function workspaceModule(): string
    {
        $group = property_exists(static::class, 'navigationGroup')
            ? (string) static::$navigationGroup
            : '';

        return match ($group) {
            'CRM' => 'crm',
            'Project Management' => 'project-management',
            'File Organizer' => 'documents',
            default => 'core',
        };
    }
}

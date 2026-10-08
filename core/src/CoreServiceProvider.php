<?php

namespace Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'core');
    }

    public function boot(): void
    {
        // Core schema has one migration owner in the centralized database.
        // Other applications only consume the shared tables through connection=core.
        if (filter_var(env('CORE_MIGRATIONS_OWNER', false), FILTER_VALIDATE_BOOL)) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations/shared');
        }

        $applicationMigrationSet = env('APP_MIGRATION_SET', 'workspace');
        $migrationDirectories = [
            'workspace' => 'file-organizer',
            'file-organizer' => 'file-organizer',
            'project-management' => 'project-management',
        ];
        if (isset($migrationDirectories[$applicationMigrationSet])) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations/'.$migrationDirectories[$applicationMigrationSet]);
        }

        foreach ([
            'User',
            'Company',
            'Vendor',
            'Project',
            'Roles',
            'Setting',
            'ExternalIdentity',
        ] as $model) {
            $legacy = 'App\\Models\\'.$model;
            $shared = 'Core\\Models\\'.$model;

            if (! class_exists($legacy, false)) {
                class_alias($shared, $legacy);
            }
        }
    }
}

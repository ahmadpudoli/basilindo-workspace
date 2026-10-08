<?php

namespace App\Providers;

use App\Http\Controllers\Auth\SsoLogoutResponse;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;
use Core\Models\Role;
use App\Services\Audit\AuditLogger;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, SsoLogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Role::created(fn (Role $role) => app(AuditLogger::class)->record('permission.role_created', $role, null, ['role_id' => $role->getKey()]));
        Role::updated(fn (Role $role) => app(AuditLogger::class)->record('permission.role_updated', $role, null, ['role_id' => $role->getKey(), 'changed_fields' => array_keys($role->getChanges())]));
        Role::deleted(fn (Role $role) => app(AuditLogger::class)->record('permission.role_deleted', $role, null, ['role_id' => $role->getKey()]));

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => view('filament.components.application-launcher', [
                'applications' => config('sso.enabled')
                    ? data_get(auth()->user()?->externalIdentities()->latest('last_login_at')->first()?->claims ?? [], 'applications.available', [])
                    : [],
            ])->render(),
        );

        FilamentShield::buildPermissionKeyUsing(
            function (string $entity, string $affix, string $subject, string $case, string $separator) {
                return match(true) {
                    # if `configurePermissionIdentifierUsing()` was used previously, then this needs to be adjusted accordingly
                    is_subclass_of($entity, Resource::class) => Str::of($affix)
                        ->snake()
                        ->append('_')
                        ->append(
                            Str::of($entity)
                                ->afterLast('\\')
                                ->beforeLast('Resource')
                                ->replace('\\', '')
                                ->snake()
                                ->replace('_', '::')
                        )
                        ->toString(),
                    is_subclass_of($entity, Page::class) => Str::of('page_')
                        ->append(class_basename($entity))
                        ->toString(),
                    is_subclass_of($entity, Widget::class) => Str::of('widget_')
                        ->append(class_basename($entity))
                        ->toString()
                    };
            });
    }
}

<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\Core\Application\Services\WorkspaceModuleRegistry;
use Core\Models\Company;
use Core\Models\Permission;
use Core\Models\Role;
use Core\Models\Setting;
use Core\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdministrationController extends Controller
{
    public function settings(): View
    {
        $this->authorizeSettings();
        return view('modules.core.settings', ['enabledModules' => app(WorkspaceModuleRegistry::class)->enabledKeys()]);
    }

    public function settingsModules(): View
    {
        $this->authorizeSettings();
        return view('modules.core.settings-modules', ['enabledModules' => app(WorkspaceModuleRegistry::class)->enabledKeys()]);
    }

    public function settingsModulesUpdate(Request $request, WorkspaceModuleRegistry $registry, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeSettings();
        $data = $request->validate(['active_modules' => ['required', 'array', 'min:1'], 'active_modules.*' => ['string', 'in:company,crm,project-management,documents']]);
        $registry->saveEnabledKeys($data['active_modules']);
        $audit->record('workspace.modules.updated', null, null, ['active_modules' => array_values($data['active_modules'])]);
        return back()->with('status', 'Daftar modul aktif berhasil diperbarui.');
    }

    private function authorizeSettings(): void
    {
        abort_unless(auth()->user()?->hasRole('super_admin') || auth()->user()?->can('access_settings'), 403);
    }

    public function users(Request $request): View
    {
        Gate::authorize('viewAny', User::class);
        $search = $request->string('q')->trim()->toString();
        $selectedRoles = array_values(array_unique(array_filter((array) $request->input('role', []), 'is_string')));
        $status = in_array($request->string('status')->toString(), ['active', 'system'], true) ? $request->string('status')->toString() : null;
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['name', 'email', 'status'], true) ? $request->string('sort')->toString() : 'name';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $users = User::query()->with(['roles', 'companies'])
            ->when($search, fn ($query) => $query->where(fn ($nested) => $nested->where('name', 'ilike', '%'.$search.'%')->orWhere('email', 'ilike', '%'.$search.'%')))
            ->when($selectedRoles, fn ($query) => $query->whereHas('roles', fn ($roles) => $roles->whereIn('name', $selectedRoles)))
            ->when($status === 'system', fn ($query) => $query->where('is_system_account', true))
            ->when($status === 'active', fn ($query) => $query->where('is_system_account', false))
            ->orderBy($sort === 'status' ? 'is_system_account' : $sort, $direction)
            ->orderBy('name')->paginate($perPage)->withQueryString();

        return view('modules.core.users', compact('users', 'search', 'selectedRoles', 'status', 'perPage', 'sort', 'direction') + ['roles' => Role::query()->orderBy('name')->get(['name'])]);
    }
    public function userCreate(): View { Gate::authorize('create', User::class); return view('modules.core.user-form', $this->userFormData()); }
    public function userStore(Request $request, AuditLogger $audit): RedirectResponse
    {
        Gate::authorize('create', User::class);
        $data = $request->validate(['name' => 'required|string|max:255', 'email' => ['required', 'email', 'max:255', $this->uniqueUserEmail()], 'password' => 'required|string|min:8|confirmed', 'roles' => 'nullable|array', 'roles.*' => $this->modelExistsRule(Role::class, 'name'), 'company_ids' => 'nullable|array', 'company_ids.*' => ['integer', $this->modelExistsRule(Company::class, 'id')->where(fn ($query) => $query->where('entity_type', 'company')->where('classification', 'internal')->where('status', 'active'))], 'permissions' => 'nullable|array', 'permissions.*' => $this->modelExistsRule(Permission::class, 'name')]);
        $roles = $data['roles'] ?? []; $companyIds = $data['company_ids'] ?? []; $permissions = $data['permissions'] ?? [];
        unset($data['roles'], $data['company_ids'], $data['permissions']); $data['password'] = Hash::make($data['password']);
        $user = User::create($data); if ($roles) $user->syncRoles($roles); if ($permissions) $user->syncPermissions($permissions); $user->companies()->sync(array_fill_keys($companyIds, ['scope_role' => 'member'])); $audit->record('user.created', $user, null, ['role_count' => count($roles), 'company_count' => count($companyIds)], null, $this->userSnapshot($user));
        return redirect()->route('workspace.core.users')->with('status', 'Pengguna berhasil dibuat.');
    }
    public function userEdit(User $user): View { Gate::authorize('view', $user); return view('modules.core.user-form', $this->userFormData($user) + ['action' => route('workspace.core.users.update', $user), 'method' => 'PUT']); }
    public function userUpdate(Request $request, User $user, AuditLogger $audit): RedirectResponse
    {
        Gate::authorize('update', $user);
        $data = $request->validate(['name' => 'required|string|max:255', 'email' => ['required', 'email', 'max:255', $this->uniqueUserEmail($user)], 'password' => 'nullable|string|min:8|confirmed', 'roles' => 'nullable|array', 'roles.*' => $this->modelExistsRule(Role::class, 'name'), 'company_ids' => 'nullable|array', 'company_ids.*' => ['integer', $this->modelExistsRule(Company::class, 'id')->where(fn ($query) => $query->where('entity_type', 'company')->where('classification', 'internal')->where('status', 'active'))], 'permissions' => 'nullable|array', 'permissions.*' => $this->modelExistsRule(Permission::class, 'name')]);
        $roles = $data['roles'] ?? []; $companyIds = $data['company_ids'] ?? []; $permissions = $data['permissions'] ?? [];
        unset($data['roles'], $data['company_ids'], $data['permissions']); if (filled($data['password'] ?? null)) $data['password'] = Hash::make($data['password']); else unset($data['password']);
        $before = $this->userSnapshot($user); $user->update($data); if (! $user->isProtectedSystemAccount()) { $user->syncRoles($roles); $user->syncPermissions($permissions); $user->companies()->sync(array_fill_keys($companyIds, ['scope_role' => 'member'])); } $user->refresh(); $audit->recordChange('user.updated', $user, $before, $this->userSnapshot($user));
        return redirect()->route('workspace.core.users')->with('status', 'Pengguna berhasil diperbarui.');
    }
    public function userDestroy(User $user, AuditLogger $audit): RedirectResponse { Gate::authorize('delete', $user); $before = $this->userSnapshot($user); $user->delete(); $audit->recordChange('user.archived', $user, $before, ['deleted_at' => now()->toIso8601String()]); return back()->with('status', 'Pengguna dipindahkan ke arsip.'); }
    public function roles(Request $request): View
    {
        Gate::authorize('viewAny', Role::class);

        $guard = in_array($request->string('guard')->toString(), ['web'], true)
            ? $request->string('guard')->toString()
            : null;
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true)
            ? $request->integer('limit')
            : 10;

        $roles = Role::query()
            ->withCount('users')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'ilike', '%'.$request->string('q')->toString().'%'))
            ->when($guard, fn ($query) => $query->where('guard_name', $guard))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('modules.core.roles', compact('roles', 'guard', 'perPage'));
    }
    public function roleCreate(): View { Gate::authorize('create', Role::class); return view('modules.core.role-form', ['permissions' => Permission::query()->orderBy('name')->get()]); }
    public function roleStore(Request $request): RedirectResponse { Gate::authorize('create', Role::class); $data = $request->validate(['name' => ['required', 'string', 'max:255', $this->uniqueRoleName()], 'guard_name' => 'required|string|max:50', 'permissions' => 'nullable|array', 'permissions.*' => ['string', $this->modelExistsRule(Permission::class, 'name')]]); $permissions = $data['permissions'] ?? []; unset($data['permissions']); $role = Role::create($data); if ($permissions) $role->syncPermissions($permissions); return redirect()->route('workspace.core.roles')->with('status', 'Role berhasil dibuat.'); }
    public function roleEdit(Role $role): View { Gate::authorize('view', $role); return view('modules.core.role-form', ['role' => $role, 'permissions' => Permission::query()->orderBy('name')->get()]); }
    public function roleUpdate(Request $request, Role $role): RedirectResponse { Gate::authorize('update', $role); $data = $request->validate(['name' => ['required', 'string', 'max:255', $this->uniqueRoleName($role)], 'guard_name' => 'required|string|max:50', 'permissions' => 'nullable|array', 'permissions.*' => ['string', $this->modelExistsRule(Permission::class, 'name')]]); $permissions = $data['permissions'] ?? []; unset($data['permissions']); if ($role->name === 'super_admin') { $data['name'] = 'super_admin'; $data['guard_name'] = 'web'; } $role->update($data); if ($role->name === 'super_admin') $role->syncPermissions(Permission::all()); else $role->syncPermissions($permissions); return redirect()->route('workspace.core.roles')->with('status', 'Role berhasil diperbarui.'); }
    public function roleDestroy(Role $role): RedirectResponse { Gate::authorize('delete', $role); abort_if($role->name === 'super_admin', 403, 'Role super_admin tidak dapat dihapus.'); $role->delete(); return back()->with('status', 'Role berhasil dihapus.'); }

    private function userFormData(?User $user = null): array
    {
        return ['user' => $user, 'roles' => Role::query()->orderBy('name')->get(), 'permissions' => Permission::query()->orderBy('name')->get(), 'companies' => Company::query()->where('entity_type', 'company')->where('classification', 'internal')->where('status', 'active')->orderBy('name')->get()];
    }

    private function userSnapshot(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'email_verified_at', 'deleted_at']);
    }

    private function uniqueUserEmail(?User $user = null)
    {
        $model = $user ?: new User;
        $rule = Rule::unique($model->getConnectionName().'.'.$model->getTable(), 'email');

        return $user ? $rule->ignore($user->getKey()) : $rule;
    }

    private function uniqueRoleName(?Role $role = null)
    {
        $model = $role ?: new Role;
        $rule = Rule::unique($model->getConnectionName().'.'.$model->getTable(), 'name');

        return $role ? $rule->ignore($role->getKey()) : $rule;
    }

    private function modelExistsRule(string $modelClass, string $column)
    {
        $model = new $modelClass;

        return Rule::exists($model->getConnectionName().'.'.$model->getTable(), $column);
    }
}

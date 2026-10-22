<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DocumentManagement\Http\Controllers\DocumentDownloadController;
use App\Modules\DocumentManagement\Http\Controllers\DocumentPreviewController;
use App\Modules\DocumentManagement\Http\Controllers\BundleDownloadController;
use App\Modules\Core\Http\Controllers\HealthController;
use App\Modules\Core\Http\Controllers\Auth\LoginController;
use App\Modules\Core\Http\Controllers\WorkspaceController;
use App\Modules\Core\Http\Controllers\AdministrationController;
use App\Modules\Company\Http\Controllers\CompanyDashboardController;
use App\Modules\Company\Http\Controllers\CompanyController;
use App\Modules\Company\Http\Controllers\VendorController;
use App\Modules\CRM\Http\Controllers\CrmDashboardController;
use App\Modules\CRM\Http\Controllers\CrmController;
use App\Modules\ProjectManagement\Http\Controllers\ProjectManagementDashboardController;
use App\Modules\ProjectManagement\Http\Controllers\ProjectManagementController;
use App\Modules\ProjectManagement\Http\Controllers\ProjectAnalyticsController;
use App\Modules\ProjectManagement\Http\Controllers\TicketDataController;
use App\Modules\DocumentManagement\Http\Controllers\DocumentManagementDashboardController;
use App\Modules\DocumentManagement\Http\Controllers\DocumentManagementController;

Route::get('health/ready', [HealthController::class, 'ready'])->name('health.ready');
Route::view('/', 'welcome')->name('home');
Route::get('login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Compatibility redirects for bookmarks and links from the previous panel URL.
Route::redirect('/admin/login', '/login');
Route::redirect('/admin', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WorkspaceController::class, 'dashboard'])->name('dashboard');
    Route::get('/workspace/settings', [AdministrationController::class, 'settings'])->name('workspace.settings');
    Route::get('/workspace/settings/modules', [AdministrationController::class, 'settingsModules'])->name('workspace.settings.modules');
    Route::put('/workspace/settings/modules', [AdministrationController::class, 'settingsModulesUpdate'])->name('workspace.settings.modules.update');
    Route::get('/workspace/company', CompanyDashboardController::class)->name('workspace.company');
    Route::get('/workspace/company/companies', [CompanyController::class, 'index'])->name('workspace.company.companies');
    Route::get('/workspace/company/companies/create', [CompanyController::class, 'create'])->name('workspace.company.create');
    Route::post('/workspace/company/companies', [CompanyController::class, 'store'])->name('workspace.company.store');
    Route::get('/workspace/company/companies/{company}', [CompanyController::class, 'show'])->name('workspace.company.show');
    Route::get('/workspace/company/companies/{company}/logo', [CompanyController::class, 'logo'])->name('workspace.company.logo');
    Route::get('/workspace/company/companies/{company}/edit', [CompanyController::class, 'edit'])->name('workspace.company.edit');
    Route::put('/workspace/company/companies/{company}', [CompanyController::class, 'update'])->name('workspace.company.update');
    Route::delete('/workspace/company/companies/{company}', [CompanyController::class, 'destroy'])->name('workspace.company.destroy');
    Route::get('/workspace/company/vendors', [VendorController::class, 'index'])->name('workspace.company.vendors');
    Route::get('/workspace/company/vendors/create', [VendorController::class, 'create'])->name('workspace.company.vendors.create');
    Route::post('/workspace/company/vendors', [VendorController::class, 'store'])->name('workspace.company.vendors.store');
    Route::get('/workspace/company/vendors/{vendor}', [VendorController::class, 'show'])->name('workspace.company.vendors.show');
    Route::get('/workspace/company/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('workspace.company.vendors.edit');
    Route::put('/workspace/company/vendors/{vendor}', [VendorController::class, 'update'])->name('workspace.company.vendors.update');
    Route::delete('/workspace/company/vendors/{vendor}', [VendorController::class, 'destroy'])->name('workspace.company.vendors.destroy');
    Route::get('/workspace/crm', CrmDashboardController::class)->name('workspace.crm');
    Route::get('/workspace/crm/accounts/{id}', [CrmController::class, 'show'])->name('workspace.crm.account.show');
    Route::get('/workspace/crm/{resource}', [CrmController::class, 'index'])->whereIn('resource', ['accounts', 'contacts', 'leads', 'opportunities', 'activities'])->name('workspace.crm.resource');
    Route::get('/workspace/crm/{resource}/create', [CrmController::class, 'create'])->whereIn('resource', ['accounts', 'contacts', 'leads', 'opportunities', 'activities'])->name('workspace.crm.create');
    Route::post('/workspace/crm/{resource}', [CrmController::class, 'store'])->whereIn('resource', ['accounts', 'contacts', 'leads', 'opportunities', 'activities'])->name('workspace.crm.store');
    Route::get('/workspace/crm/{resource}/{id}/edit', [CrmController::class, 'edit'])->whereIn('resource', ['accounts', 'contacts', 'leads', 'opportunities', 'activities'])->name('workspace.crm.edit');
    Route::put('/workspace/crm/{resource}/{id}', [CrmController::class, 'update'])->whereIn('resource', ['accounts', 'contacts', 'leads', 'opportunities', 'activities'])->name('workspace.crm.update');
    Route::delete('/workspace/crm/{resource}/{id}', [CrmController::class, 'destroy'])->whereIn('resource', ['accounts', 'contacts', 'leads', 'opportunities', 'activities'])->name('workspace.crm.destroy');
    Route::get('/workspace/project-management', ProjectManagementDashboardController::class)->name('workspace.project-management');
    Route::get('/workspace/project-management/board', [ProjectManagementController::class, 'board'])->name('workspace.pm.board');
    Route::get('/workspace/project-management/timeline', [ProjectManagementController::class, 'timeline'])->name('workspace.pm.timeline');
    Route::get('/workspace/project-management/tickets/export', [TicketDataController::class, 'export'])->name('workspace.pm.tickets.export');
    Route::post('/workspace/project-management/tickets/import', [TicketDataController::class, 'import'])->name('workspace.pm.tickets.import');
    Route::get('/workspace/project-management/contributions', [ProjectAnalyticsController::class, 'contributions'])->name('workspace.pm.contributions');
    Route::get('/workspace/project-management/leaderboard', [ProjectAnalyticsController::class, 'leaderboard'])->name('workspace.pm.leaderboard');
    Route::get('/workspace/project-management/projects/{id}', [ProjectManagementController::class, 'showProject'])->name('workspace.pm.project.show');
    Route::post('/workspace/project-management/projects/{id}/members', [ProjectManagementController::class, 'addMember'])->name('workspace.pm.project.members.add');
    Route::delete('/workspace/project-management/projects/{id}/members/{userId}', [ProjectManagementController::class, 'removeMember'])->name('workspace.pm.project.members.remove');
    Route::get('/workspace/project-management/{resource}', [ProjectManagementController::class, 'index'])->whereIn('resource', ['projects', 'tickets', 'epics', 'notes', 'statuses', 'priorities', 'comments', 'notifications'])->name('workspace.pm.resource');
    Route::get('/workspace/project-management/{resource}/create', [ProjectManagementController::class, 'create'])->whereIn('resource', ['projects', 'tickets', 'epics', 'notes', 'statuses', 'priorities', 'comments', 'notifications'])->name('workspace.pm.create');
    Route::post('/workspace/project-management/{resource}', [ProjectManagementController::class, 'store'])->whereIn('resource', ['projects', 'tickets', 'epics', 'notes', 'statuses', 'priorities', 'comments', 'notifications'])->name('workspace.pm.store');
    Route::get('/workspace/project-management/{resource}/{id}/edit', [ProjectManagementController::class, 'edit'])->whereIn('resource', ['projects', 'tickets', 'epics', 'notes', 'statuses', 'priorities', 'comments', 'notifications'])->name('workspace.pm.edit');
    Route::put('/workspace/project-management/{resource}/{id}', [ProjectManagementController::class, 'update'])->whereIn('resource', ['projects', 'tickets', 'epics', 'notes', 'statuses', 'priorities', 'comments', 'notifications'])->name('workspace.pm.update');
    Route::delete('/workspace/project-management/{resource}/{id}', [ProjectManagementController::class, 'destroy'])->whereIn('resource', ['projects', 'tickets', 'epics', 'notes', 'statuses', 'priorities', 'comments', 'notifications'])->name('workspace.pm.destroy');
    Route::get('/workspace/documents', DocumentManagementDashboardController::class)->name('workspace.documents');
    Route::get('/workspace/documents/create', [DocumentManagementController::class, 'create'])->name('workspace.documents.create');
    Route::post('/workspace/documents', [DocumentManagementController::class, 'store'])->name('workspace.documents.store');
    Route::get('/workspace/documents/{document}/edit', [DocumentManagementController::class, 'edit'])->name('workspace.documents.edit');
    Route::put('/workspace/documents/{document}', [DocumentManagementController::class, 'update'])->name('workspace.documents.update');
    Route::delete('/workspace/documents/{document}', [DocumentManagementController::class, 'destroy'])->name('workspace.documents.destroy');
    Route::get('/workspace/documents/list', [DocumentManagementController::class, 'documents'])->name('workspace.documents.list');
    Route::get('/workspace/documents/types', [DocumentManagementController::class, 'types'])->name('workspace.documents.types');
    Route::get('/workspace/documents/types/create', [DocumentManagementController::class, 'typeCreate'])->name('workspace.documents.types.create');
    Route::post('/workspace/documents/types', [DocumentManagementController::class, 'typeStore'])->name('workspace.documents.types.store');
    Route::get('/workspace/documents/types/{type}/edit', [DocumentManagementController::class, 'typeEdit'])->name('workspace.documents.types.edit');
    Route::put('/workspace/documents/types/{type}', [DocumentManagementController::class, 'typeUpdate'])->name('workspace.documents.types.update');
    Route::delete('/workspace/documents/types/{type}', [DocumentManagementController::class, 'typeDestroy'])->name('workspace.documents.types.destroy');
    Route::get('/workspace/documents/bundles', [DocumentManagementController::class, 'bundles'])->name('workspace.documents.bundles');
    Route::get('/workspace/documents/bundles/create', [DocumentManagementController::class, 'bundleCreate'])->name('workspace.documents.bundles.create');
    Route::post('/workspace/documents/bundles', [DocumentManagementController::class, 'bundleStore'])->name('workspace.documents.bundles.store');

    Route::get('/workspace', function () {
        session()->forget('workspace.active_module');

        return redirect()->route('dashboard');
    })->name('workspace.launcher');
    Route::get('/workspace/core/users', [AdministrationController::class, 'users'])->name('workspace.core.users');
    Route::get('/workspace/core/users/create', [AdministrationController::class, 'userCreate'])->name('workspace.core.users.create');
    Route::post('/workspace/core/users', [AdministrationController::class, 'userStore'])->name('workspace.core.users.store');
    Route::get('/workspace/core/users/{user}/edit', [AdministrationController::class, 'userEdit'])->name('workspace.core.users.edit');
    Route::put('/workspace/core/users/{user}', [AdministrationController::class, 'userUpdate'])->name('workspace.core.users.update');
    Route::delete('/workspace/core/users/{user}', [AdministrationController::class, 'userDestroy'])->name('workspace.core.users.destroy');
    Route::get('/workspace/core/roles', [AdministrationController::class, 'roles'])->name('workspace.core.roles');
    Route::get('/workspace/core/roles/create', [AdministrationController::class, 'roleCreate'])->name('workspace.core.roles.create');
    Route::post('/workspace/core/roles', [AdministrationController::class, 'roleStore'])->name('workspace.core.roles.store');
    Route::get('/workspace/core/roles/{role}/edit', [AdministrationController::class, 'roleEdit'])->name('workspace.core.roles.edit');
    Route::put('/workspace/core/roles/{role}', [AdministrationController::class, 'roleUpdate'])->name('workspace.core.roles.update');
    Route::delete('/workspace/core/roles/{role}', [AdministrationController::class, 'roleDestroy'])->name('workspace.core.roles.destroy');

    Route::get('/workspace/{module}', function (string $module) {
        $destinations = [
            'core' => route('dashboard'),
            'company' => route('workspace.company'),
            'crm' => route('workspace.crm'),
            'project-management' => route('workspace.project-management'),
            'documents' => route('workspace.documents'),
        ];

        abort_unless(isset($destinations[$module]), 404);
        abort_unless(app(\App\Modules\Core\Application\Services\WorkspaceModuleRegistry::class)->isEnabled($module), 404);
        session(['workspace.active_module' => $module]);

        return redirect($destinations[$module]);
    })->whereIn('module', ['core', 'company', 'crm', 'project-management', 'documents'])
        ->name('workspace.module');
});

Route::middleware('auth')->group(function () {
    Route::get('documents/{document}/download', DocumentDownloadController::class)->name('documents.download');
    Route::get('documents/{document}/preview', DocumentPreviewController::class)->name('documents.preview');
    Route::get('bundles/{bundle}/download', BundleDownloadController::class)->name('bundles.download');
});


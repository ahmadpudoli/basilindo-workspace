<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DocumentManagement\Http\Controllers\DocumentDownloadController;
use App\Modules\DocumentManagement\Http\Controllers\DocumentPreviewController;
use App\Modules\DocumentManagement\Http\Controllers\BundleDownloadController;
use App\Modules\Core\Http\Controllers\HealthController;
use App\Modules\Core\Http\Controllers\Auth\LoginController;
use App\Modules\Core\Http\Controllers\WorkspaceController;
use App\Modules\Core\Http\Controllers\AdministrationController;
use App\Modules\Core\Http\Controllers\AuditController;
use App\Modules\Core\Http\Controllers\NotificationController;
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
use App\Modules\HRIS\Application\Services\HrisDashboardController;
use App\Modules\HRIS\Application\Services\EmployeeController;
use App\Modules\HRIS\Application\Services\EmployeeDocumentController;
use App\Modules\HRIS\Application\Services\LeaveRequestController;
use App\Modules\HRIS\Application\Services\LeaveSummaryController;
use App\Modules\HRIS\Application\Services\HrisOperationsController;
use App\Modules\HRIS\Application\Services\HrisSettingsController;
use App\Modules\HRIS\Application\Services\WebPushController;

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
    Route::get('/workspace/core/audit-log', [AuditController::class, 'index'])->name('workspace.core.audit-log');
    Route::get('/workspace/notifications', [NotificationController::class, 'index'])->name('workspace.notifications');
    Route::get('/workspace/notifications/{notification}', [NotificationController::class, 'show'])->name('workspace.notifications.show');
    Route::post('/workspace/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('workspace.notifications.read-all');
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
    Route::get('/workspace/documents/types/{type}', [DocumentManagementController::class, 'typeShow'])->name('workspace.documents.types.show');
    Route::get('/workspace/documents/bundles', [DocumentManagementController::class, 'bundles'])->name('workspace.documents.bundles');
    Route::get('/workspace/documents/bundles/create', [DocumentManagementController::class, 'bundleCreate'])->name('workspace.documents.bundles.create');
    Route::post('/workspace/documents/bundles', [DocumentManagementController::class, 'bundleStore'])->name('workspace.documents.bundles.store');
    Route::get('/workspace/documents/{document}', [DocumentManagementController::class, 'show'])->name('workspace.documents.show');
    Route::get('/workspace/hris', HrisDashboardController::class)->name('workspace.hris');
    Route::get('/workspace/hris/biodata', [HrisDashboardController::class, 'biodata'])->name('workspace.hris.biodata');
    Route::get('/workspace/hris/employees', [EmployeeController::class, 'index'])->name('workspace.hris.employees');
    Route::get('/workspace/hris/employees/create', [EmployeeController::class, 'create'])->name('workspace.hris.employees.create');
    Route::post('/workspace/hris/employees', [EmployeeController::class, 'store'])->name('workspace.hris.employees.store');
    Route::get('/workspace/hris/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('workspace.hris.employees.edit');
    Route::put('/workspace/hris/employees/{employee}', [EmployeeController::class, 'update'])->name('workspace.hris.employees.update');
    Route::delete('/workspace/hris/employees/{employee}', [EmployeeController::class, 'destroy'])->name('workspace.hris.employees.destroy');
    Route::get('/workspace/hris/employee-documents', [EmployeeDocumentController::class, 'index'])->name('workspace.hris.employee-documents');
    Route::get('/workspace/hris/employee-documents/create', [EmployeeDocumentController::class, 'create'])->name('workspace.hris.employee-documents.create');
    Route::post('/workspace/hris/employee-documents', [EmployeeDocumentController::class, 'store'])->name('workspace.hris.employee-documents.store');
    Route::get('/workspace/hris/employee-documents/{employeeDocument}/download', [EmployeeDocumentController::class, 'download'])->name('workspace.hris.employee-documents.download');
    Route::delete('/workspace/hris/employee-documents/{employeeDocument}', [EmployeeDocumentController::class, 'destroy'])->name('workspace.hris.employee-documents.destroy');
    Route::get('/workspace/hris/leave-requests', [LeaveRequestController::class, 'index'])->name('workspace.hris.leave-requests');
    Route::get('/workspace/hris/leave-summary', [LeaveRequestController::class, 'summary'])->name('workspace.hris.leave-summary');
    Route::get('/workspace/hris/leave-summary/{leaveRequest}', [LeaveSummaryController::class, 'show'])->name('workspace.hris.leave-summary.show');
    Route::get('/workspace/hris/leave-requests/create', [LeaveRequestController::class, 'create'])->name('workspace.hris.leave-requests.create');
    Route::post('/workspace/hris/leave-requests', [LeaveRequestController::class, 'store'])->name('workspace.hris.leave-requests.store');
    Route::get('/workspace/hris/leave-requests/{leaveRequest}/edit', [LeaveRequestController::class, 'edit'])->name('workspace.hris.leave-requests.edit');
    Route::put('/workspace/hris/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'resubmit'])->name('workspace.hris.leave-requests.resubmit');
    Route::get('/workspace/hris/leave-requests/{leaveRequest}/supporting-document', [LeaveRequestController::class, 'supportingDocument'])->name('workspace.hris.leave-requests.supporting-document');
    Route::get('/workspace/hris/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('workspace.hris.leave-requests.show');
    Route::get('/workspace/hris/overtime', [HrisOperationsController::class, 'overtime'])->name('workspace.hris.overtime');
    Route::get('/workspace/hris/overtime-summary', [HrisOperationsController::class, 'overtimeSummary'])->name('workspace.hris.overtime-summary');
    Route::get('/workspace/hris/overtime/create', [HrisOperationsController::class, 'overtimeCreate'])->name('workspace.hris.overtime.create');
    Route::post('/workspace/hris/overtime', [HrisOperationsController::class, 'overtimeStore'])->name('workspace.hris.overtime.store');
    Route::get('/workspace/hris/claims', [HrisOperationsController::class, 'claims'])->name('workspace.hris.claims');
    Route::get('/workspace/hris/claims/create', [HrisOperationsController::class, 'claimCreate'])->name('workspace.hris.claims.create');
    Route::post('/workspace/hris/claims', [HrisOperationsController::class, 'claimStore'])->name('workspace.hris.claims.store');
    Route::get('/workspace/hris/approvals', [HrisOperationsController::class, 'approvals'])->name('workspace.hris.approvals');
    Route::get('/workspace/hris/approvals/{type}/{id}/{step}', [HrisOperationsController::class, 'approvalDetail'])->whereIn('type', ['leave', 'overtime', 'claim'])->whereIn('step', ['manager', 'hr'])->name('workspace.hris.approval-detail');
    Route::get('/workspace/hris/{type}/{id}/supporting-document/{step}', [HrisOperationsController::class, 'supportingDocument'])->name('workspace.hris.supporting-document');
    Route::post('/workspace/hris/{type}/{id}/approve/{step}', [HrisOperationsController::class, 'approve'])->name('workspace.hris.approve');
    Route::post('/workspace/hris/claims/{id}/accountability', [HrisOperationsController::class, 'accountability'])->name('workspace.hris.claims.accountability');
    Route::get('/workspace/settings/hris', [HrisSettingsController::class, 'index'])->name('workspace.settings.hris');
    Route::get('/workspace/settings/hris/workflow', [HrisSettingsController::class, 'workflow'])->name('workspace.settings.hris.workflow');
    Route::get('/workspace/settings/hris/workflow/{approver}/edit', [HrisSettingsController::class, 'workflowEdit'])->name('workspace.settings.hris.workflow.edit');
    Route::post('/workspace/settings/hris', [HrisSettingsController::class, 'store'])->name('workspace.settings.hris.store');
    Route::post('/workspace/settings/hris/workflow/leave-sick', [HrisSettingsController::class, 'workflowStore'])->name('workspace.settings.hris.workflow.store');
    Route::patch('/workspace/settings/hris/workflow/{approver}', [HrisSettingsController::class, 'workflowUpdate'])->name('workspace.settings.hris.workflow.update');
    Route::delete('/workspace/settings/hris/workflow/{approver}', [HrisSettingsController::class, 'workflowArchive'])->name('workspace.settings.hris.workflow.archive');
    Route::get('/workspace/settings/hris/{setting}/edit', [HrisSettingsController::class, 'edit'])->name('workspace.settings.hris.edit');
    Route::put('/workspace/settings/hris/{setting}', [HrisSettingsController::class, 'update'])->name('workspace.settings.hris.update');
    Route::patch('/workspace/settings/hris/{setting}/archive', [HrisSettingsController::class, 'archive'])->name('workspace.settings.hris.archive');
    Route::patch('/workspace/settings/hris/{setting}/restore', [HrisSettingsController::class, 'restore'])->name('workspace.settings.hris.restore');
    Route::redirect('/workspace/hris/settings', '/workspace/settings/hris')->name('workspace.hris.settings.legacy');
    Route::post('/workspace/hris/settings', [HrisSettingsController::class, 'store'])->name('workspace.hris.settings.store.legacy');
    Route::get('/workspace/notifications/push/config', [WebPushController::class, 'config'])->name('workspace.push.config');
    Route::post('/workspace/notifications/push/subscriptions', [WebPushController::class, 'store'])->name('workspace.push.store');
    Route::delete('/workspace/notifications/push/subscriptions', [WebPushController::class, 'destroy'])->name('workspace.push.destroy');

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
            'hris' => route('workspace.hris'),
        ];

        abort_unless(isset($destinations[$module]), 404);
        abort_unless(app(\App\Modules\Core\Application\Services\WorkspaceModuleRegistry::class)->isEnabled($module), 404);
        session(['workspace.active_module' => $module]);

        return redirect($destinations[$module]);
    })->whereIn('module', ['core', 'company', 'crm', 'project-management', 'documents', 'hris'])
        ->name('workspace.module');
});

Route::middleware('auth')->group(function () {
    Route::get('documents/{document}/download', DocumentDownloadController::class)->name('documents.download');
    Route::get('documents/{document}/preview', DocumentPreviewController::class)->name('documents.preview');
    Route::get('bundles/{bundle}/download', BundleDownloadController::class)->name('bundles.download');
});


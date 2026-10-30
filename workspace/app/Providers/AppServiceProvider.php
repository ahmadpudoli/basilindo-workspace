<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Livewire\Livewire;
use App\Modules\DocumentManagement\Livewire\DocumentMetadataFields;
use Illuminate\Support\Facades\Gate;
use Core\Models\Role;
use Core\Models\Project;
use Core\Models\User;
use Core\Models\Company;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\Core\Application\Services\Audit\AuditModelObserver;
use App\Modules\Core\Policies\RolePolicy;
use App\Modules\Core\Policies\UserPolicy;
use App\Modules\Company\Policies\CompanyPolicy;
use App\Modules\Company\Policies\VendorPolicy;
use Core\Models\Vendor;
use App\Modules\CRM\Domain\Models\CrmAccount;
use App\Modules\CRM\Domain\Models\CrmActivity;
use App\Modules\CRM\Domain\Models\CrmContact;
use App\Modules\CRM\Domain\Models\CrmLead;
use App\Modules\CRM\Domain\Models\CrmOpportunity;
use App\Modules\CRM\Policies\CrmAccountPolicy;
use App\Modules\CRM\Policies\CrmActivityPolicy;
use App\Modules\CRM\Policies\CrmContactPolicy;
use App\Modules\CRM\Policies\CrmLeadPolicy;
use App\Modules\CRM\Policies\CrmOpportunityPolicy;
use App\Modules\DocumentManagement\Domain\Models\Bundle;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use App\Modules\DocumentManagement\Policies\BundlePolicy;
use App\Modules\DocumentManagement\Policies\DocumentPolicy;
use App\Modules\DocumentManagement\Policies\DocumentTypePolicy;
use App\Modules\ProjectManagement\Domain\Models\Ticket;
use App\Modules\ProjectManagement\Policies\ProjectPolicy;
use App\Modules\ProjectManagement\Policies\TicketPolicy;
use App\Modules\HRIS\Domain\Models\Employee;
use App\Modules\HRIS\Domain\Models\EmployeeDocument;
use App\Modules\HRIS\Domain\Models\HrisHoliday;
use App\Modules\HRIS\Domain\Models\LeaveRequest;
use App\Modules\HRIS\Application\Services\EmployeePolicy;
use App\Modules\HRIS\Application\Services\LeaveRequestPolicy;
use App\Modules\HRIS\Application\Services\OvertimePolicy;
use App\Modules\HRIS\Application\Services\ClaimPolicy;
use App\Modules\HRIS\Application\Services\EmployeeDocumentPolicy;
use App\Modules\HRIS\Application\Services\HrisHolidayPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Livewire::component('document-metadata-fields', DocumentMetadataFields::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Vendor::class, VendorPolicy::class);
        Gate::policy(CrmAccount::class, CrmAccountPolicy::class);
        Gate::policy(CrmActivity::class, CrmActivityPolicy::class);
        Gate::policy(CrmContact::class, CrmContactPolicy::class);
        Gate::policy(CrmLead::class, CrmLeadPolicy::class);
        Gate::policy(CrmOpportunity::class, CrmOpportunityPolicy::class);
        Gate::policy(Bundle::class, BundlePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(DocumentType::class, DocumentTypePolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(EmployeeDocument::class, EmployeeDocumentPolicy::class);
        Gate::policy(HrisHoliday::class, HrisHolidayPolicy::class);
        Gate::policy(LeaveRequest::class, LeaveRequestPolicy::class);
        Gate::policy(\App\Modules\HRIS\Domain\Models\OvertimeRequest::class, OvertimePolicy::class);
        Gate::policy(\App\Modules\HRIS\Domain\Models\Claim::class, ClaimPolicy::class);

        // Temporary upload Livewire disimpan pada folder private di MinIO/S3.
        config([
            'livewire.temporary_file_upload.disk' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK', 's3'),
            'livewire.temporary_file_upload.directory' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DIRECTORY', 'workspace-tmp'),
        ]);

        Role::created(fn (Role $role) => app(AuditLogger::class)->record('permission.role_created', $role, null, ['role_id' => $role->getKey()]));
        Role::updated(fn (Role $role) => app(AuditLogger::class)->record('permission.role_updated', $role, null, ['role_id' => $role->getKey(), 'changed_fields' => array_keys($role->getChanges())]));
        Role::deleted(fn (Role $role) => app(AuditLogger::class)->record('permission.role_deleted', $role, null, ['role_id' => $role->getKey()]));

        foreach ([
            \Core\Models\Company::class,
            \Core\Models\Vendor::class,
            \Core\Models\Project::class,
            \App\Modules\CRM\Domain\Models\CrmAccount::class,
            \App\Modules\CRM\Domain\Models\CrmContact::class,
            \App\Modules\CRM\Domain\Models\CrmLead::class,
            \App\Modules\CRM\Domain\Models\CrmOpportunity::class,
            \App\Modules\CRM\Domain\Models\CrmActivity::class,
            \App\Modules\ProjectManagement\Domain\Models\Ticket::class,
            \App\Modules\ProjectManagement\Domain\Models\Epic::class,
            \App\Modules\ProjectManagement\Domain\Models\ProjectNote::class,
            \App\Modules\ProjectManagement\Domain\Models\TicketStatus::class,
            \App\Modules\ProjectManagement\Domain\Models\TicketPriority::class,
            \App\Modules\ProjectManagement\Domain\Models\TicketComment::class,
            \App\Modules\ProjectManagement\Domain\Models\Notification::class,
            \App\Modules\HRIS\Domain\Models\Employee::class,
            \App\Modules\HRIS\Domain\Models\EmployeeDocument::class,
            \App\Modules\HRIS\Domain\Models\LeaveRequest::class,
            \App\Modules\HRIS\Domain\Models\LeaveRequestDate::class,
            \App\Modules\HRIS\Domain\Models\OvertimeRequest::class,
            \App\Modules\HRIS\Domain\Models\Claim::class,
            \App\Modules\HRIS\Domain\Models\HrisSetting::class,
            \App\Modules\HRIS\Domain\Models\HrisWorkflowApprover::class,
            \App\Modules\HRIS\Domain\Models\ApprovalEvent::class,
            \App\Modules\DocumentManagement\Domain\Models\DocumentVersion::class,
            \App\Modules\DocumentManagement\Domain\Models\Bundle::class,
            \App\Modules\DocumentManagement\Domain\Models\DocumentRelation::class,
            \App\Modules\DocumentManagement\Domain\Models\DocumentParty::class,
            \App\Modules\DocumentManagement\Domain\Models\VerificationCase::class,
            \App\Modules\DocumentManagement\Domain\Models\VerificationItem::class,
        ] as $auditedModel) {
            if (class_exists($auditedModel)) {
                $auditedModel::observe(AuditModelObserver::class);
            }
        }

    }
}

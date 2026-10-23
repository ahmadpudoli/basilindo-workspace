<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\WorkspaceModuleRegistry;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function dashboard(WorkspaceModuleRegistry $registry): View
    {
        $modules = [
                ['key' => 'documents', 'permission' => 'access_documents', 'label' => 'Document Management', 'description' => 'Simpan, cari, verifikasi, dan bundel dokumen.'],
                ['key' => 'crm', 'permission' => 'access_crm', 'label' => 'CRM', 'description' => 'Kelola account, lead, opportunity, dan aktivitas.'],
                ['key' => 'project-management', 'permission' => 'access_project_management', 'label' => 'Project Management', 'description' => 'Kelola project, ticket, member, dan timeline.'],
                ['key' => 'company', 'permission' => 'access_company', 'label' => 'Company', 'description' => 'Kelola master perusahaan dan company scope.'],
            ];
        if (auth()->user()?->hasRole('super_admin') || auth()->user()?->can('access_settings')) {
            $modules[] = ['key' => 'settings', 'label' => 'Pengaturan', 'description' => 'Atur modul aktif, pengguna, role, permission, dan scope data.'];
        }

        $user = auth()->user();
        $canModule = fn (array $module): bool => $module['key'] === 'settings'
            ? ($user?->hasRole('super_admin') || $user?->can('access_settings'))
            : ($registry->isEnabled($module['key']) && ($user?->hasRole('super_admin') || $user?->can($module['permission'])));

        return view('workspace.dashboard', ['modules' => array_values(array_filter($modules, $canModule))]);
    }
}

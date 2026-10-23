<?php

namespace App\Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Models\Company;
use Core\Models\Vendor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        Gate::authorize('viewAny', Company::class);
        $visibleCompanies = Company::query()->visibleTo($request->user());
        $vendors = Vendor::query()->with(['vendorCompany', 'operationalCompanies']);
        if (! $request->user()->hasRole('super_admin')) {
            $allowedCompanyIds = $request->user()->companies()->pluck('companies.id');
            $vendors->where(function ($query) use ($allowedCompanyIds): void {
                $query->whereHas('operationalCompanies', fn ($companies) => $companies->whereIn('companies.id', $allowedCompanyIds))
                    ->orWhereIn('vendors.company_id', $allowedCompanyIds);
            });
        }

        return view('modules.company.dashboard', [
            'stats' => [
                ['label' => 'Total perusahaan', 'value' => (clone $visibleCompanies)->count(), 'tone' => 'blue', 'icon' => 'company'],
                ['label' => 'Aktif', 'value' => (clone $visibleCompanies)->where('status', 'active')->count(), 'tone' => 'green', 'icon' => 'verification'],
                ['label' => 'Company internal', 'value' => (clone $visibleCompanies)->where('entity_type', 'company')->where('classification', 'internal')->count(), 'tone' => 'cyan', 'icon' => 'project'],
                ['label' => 'Group', 'value' => (clone $visibleCompanies)->where('entity_type', 'group')->count(), 'tone' => 'purple', 'icon' => 'users'],
                ['label' => 'Company external', 'value' => (clone $visibleCompanies)->where('entity_type', 'company')->where('classification', 'external')->count(), 'tone' => 'orange', 'icon' => 'company'],
                ['label' => 'Total vendor', 'value' => (clone $vendors)->count(), 'tone' => 'green', 'icon' => 'verification'],
            ],
            'companies' => (clone $visibleCompanies)->with('parent')->latest()->limit(5)->get(),
            'vendors' => $vendors->latest('vendors.created_at')->limit(5)->get(),
        ]);
    }
}

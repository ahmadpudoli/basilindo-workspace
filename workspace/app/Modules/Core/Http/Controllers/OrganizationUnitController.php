<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Http\Requests\OrganizationUnitRequest;
use Core\Models\Company;
use Core\Models\OrganizationUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationUnitController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', OrganizationUnit::class);
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['code', 'name', 'type', 'company_id', 'sort_order', 'status'], true) ? $request->string('sort')->toString() : 'sort_order';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $status = in_array($request->string('status')->toString(), ['active', 'inactive'], true) ? $request->string('status')->toString() : '';
        $companyId = $request->integer('company_id') ?: null;
        $units = OrganizationUnit::query()->visibleTo($request->user())->with(['company', 'parent'])->withCount('children')
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($nested) => $nested->where('name', 'ilike', '%'.trim($request->q).'%')->orWhere('code', 'ilike', '%'.trim($request->q).'%')))
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        return view('modules.core.organization-units', compact('units', 'companies', 'perPage', 'sort', 'direction', 'status', 'companyId'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', OrganizationUnit::class);
        return $this->form($request, new OrganizationUnit(['status' => 'active', 'type' => 'division', 'sort_order' => 0]));
    }

    public function store(OrganizationUnitRequest $request): RedirectResponse
    {
        Gate::authorize('create', OrganizationUnit::class);
        $this->authorizeCompany($request, $request->integer('company_id'));
        $unit = OrganizationUnit::create($request->validated());
        return redirect()->route('workspace.core.organization-units')->with('status', "Unit {$unit->name} berhasil dibuat.");
    }

    public function edit(Request $request, OrganizationUnit $organizationUnit): View
    {
        Gate::authorize('update', $organizationUnit);
        return $this->form($request, $organizationUnit);
    }

    public function update(OrganizationUnitRequest $request, OrganizationUnit $organizationUnit): RedirectResponse
    {
        Gate::authorize('update', $organizationUnit);
        $this->authorizeCompany($request, $request->integer('company_id'));
        $organizationUnit->update($request->validated());
        return redirect()->route('workspace.core.organization-units')->with('status', "Unit {$organizationUnit->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, OrganizationUnit $organizationUnit): RedirectResponse
    {
        Gate::authorize('delete', $organizationUnit);
        $isUsed = DB::connection('hris')->table('employees')->where('organization_unit_id', $organizationUnit->getKey())->exists();
        if ($isUsed || $organizationUnit->children()->exists()) {
            $organizationUnit->update(['status' => 'inactive']);
            return back()->with('status', "Unit {$organizationUnit->name} masih digunakan dan telah dinonaktifkan.");
        }
        $organizationUnit->delete();
        return back()->with('status', "Unit {$organizationUnit->name} berhasil diarsipkan.");
    }

    private function form(Request $request, OrganizationUnit $unit): View
    {
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        $parents = OrganizationUnit::query()->visibleTo($request->user())->where('status', 'active')->when($unit->exists, fn ($query) => $query->whereKeyNot($unit->getKey()))->orderBy('name')->get();
        return view('modules.core.organization-unit-form', compact('unit', 'companies', 'parents'));
    }

    private function authorizeCompany(Request $request, int $companyId): void
    {
        abort_unless(Company::query()->operationalFor($request->user())->whereKey($companyId)->exists(), 403);
    }
}

<?php

namespace App\Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Http\Requests\VendorRequest;
use Core\Models\Company;
use Core\Models\User;
use Core\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Vendor::class);
        $status = in_array($request->string('status')->toString(), ['active', 'inactive'], true) ? $request->string('status')->toString() : null;
        $companyId = $request->integer('company_id') ?: null;
        $perPage = in_array($request->integer('limit'), [10, 15, 25, 50], true) ? $request->integer('limit') : 10;
        $vendors = $this->scopedQuery($request->user())
            ->when($request->q, fn (Builder $query, $value) => $query->where(fn (Builder $nested) => $nested->where('vendors.name', 'ilike', "%{$value}%")->orWhere('vendors.code', 'ilike', "%{$value}%")))
            ->when($status, fn (Builder $query) => $query->where('vendors.status', $status))
            ->when($companyId, fn (Builder $query) => $query->where(function (Builder $nested) use ($companyId): void { $nested->whereHas('operationalCompanies', fn (Builder $companies) => $companies->whereKey($companyId))->orWhere('vendors.company_id', $companyId); }))
            ->latest('vendors.created_at')
            ->paginate($perPage)
            ->withQueryString();
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get(['id', 'name', 'code']);

        return view('modules.company.vendors', compact('vendors', 'companies', 'status', 'companyId', 'perPage'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Vendor::class);
        return view('modules.company.vendor-form', ['vendor' => new Vendor(), 'vendorCompanies' => $this->vendorOptions($request->user()), 'operationalCompanies' => $this->operationalOptions($request->user()), 'action' => route('workspace.company.vendors.store'), 'method' => 'POST']);
    }

    public function store(VendorRequest $request): RedirectResponse
    {
        Gate::authorize('create', Vendor::class);
        $data = $request->validated();
        $operationalIds = $data['operational_company_ids'] ?? [];
        unset($data['operational_company_ids']);
        $this->ensureCompaniesScope($request->user(), $data['vendor_company_id'], $operationalIds);
        // company_id wajib pada skema legacy; simpan perusahaan pertama sebagai anchor kompatibilitas.
        $data['company_id'] = (int) $operationalIds[0];
        $vendor = Vendor::create($data);
        $vendor->operationalCompanies()->sync($operationalIds);

        return redirect()->route('workspace.company.vendors.show', $vendor)->with('status', 'Vendor berhasil dibuat.');
    }

    public function show(Vendor $vendor): View
    {
        Gate::authorize('view', $vendor);
        $vendor->load(['vendorCompany', 'operationalCompanies']);

        return view('modules.company.vendor-show', compact('vendor'));
    }

    public function edit(Request $request, Vendor $vendor): View
    {
        Gate::authorize('update', $vendor);
        $vendor->load('operationalCompanies');
        return view('modules.company.vendor-form', ['vendor' => $vendor, 'vendorCompanies' => $this->vendorOptions($request->user()), 'operationalCompanies' => $this->operationalOptions($request->user()), 'action' => route('workspace.company.vendors.update', $vendor), 'method' => 'PUT']);
    }

    public function update(VendorRequest $request, Vendor $vendor): RedirectResponse
    {
        Gate::authorize('update', $vendor);
        $data = $request->validated();
        $operationalIds = $data['operational_company_ids'] ?? [];
        unset($data['operational_company_ids']);
        $this->ensureCompaniesScope($request->user(), $data['vendor_company_id'], $operationalIds);
        // Pertahankan anchor legacy agar data lama dan query kompatibilitas tetap berjalan.
        $data['company_id'] = (int) $operationalIds[0];
        $vendor->update($data);
        $vendor->operationalCompanies()->sync($operationalIds);

        return redirect()->route('workspace.company.vendors.show', $vendor)->with('status', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        Gate::authorize('delete', $vendor);
        $vendor->delete();

        return redirect()->route('workspace.company.vendors')->with('status', 'Vendor dipindahkan ke arsip.');
    }

    private function scopedQuery(User $user): Builder
    {
        $query = Vendor::query()->with(['vendorCompany', 'operationalCompanies']);
        if (! $user->hasRole('super_admin')) {
            $allowedIds = $user->companies()->pluck('companies.id');
            $query->where(function (Builder $nested) use ($allowedIds): void { $nested->whereHas('operationalCompanies', fn (Builder $companies) => $companies->whereIn('companies.id', $allowedIds))->orWhereIn('vendors.company_id', $allowedIds); });
        }

        return $query;
    }

    private function vendorOptions(User $user)
    {
        return Company::query()->visibleTo($user)->where('entity_type', 'company')->whereJsonContains('roles', 'vendor')->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']);
    }

    private function operationalOptions(User $user)
    {
        return Company::query()->operationalFor($user)->orderBy('name')->get(['id', 'name', 'code']);
    }

    private function ensureCompaniesScope(User $user, int $vendorCompanyId, array $operationalIds): void
    {
        abort_unless($this->vendorOptions($user)->contains('id', $vendorCompanyId), 403);
        $allowedOperationalIds = $this->operationalOptions($user)->modelKeys();
        abort_unless(count(array_diff(array_map('intval', $operationalIds), $allowedOperationalIds)) === 0 && count($operationalIds) > 0, 403);
    }

}

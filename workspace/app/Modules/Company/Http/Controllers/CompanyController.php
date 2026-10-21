<?php

namespace App\Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Company\Http\Requests\CompanyRequest;
use Core\Models\Company;
use Core\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('viewAny', Company::class);
        $sortColumns = ['name', 'code', 'entity_type', 'classification', 'status', 'created_at'];
        $sort = in_array($request->string('sort')->toString(), $sortColumns, true) ? $request->string('sort')->toString() : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $entityType = in_array($request->string('entity_type')->toString(), ['group', 'company'], true) ? $request->string('entity_type')->toString() : null;
        $classification = in_array($request->string('classification')->toString(), ['internal', 'external'], true) ? $request->string('classification')->toString() : null;
        $status = in_array($request->string('status')->toString(), ['active', 'inactive'], true) ? $request->string('status')->toString() : null;
        $roles = array_values(array_intersect((array) $request->input('roles', []), ['operating_company', 'client', 'vendor', 'partner']));
        $perPage = in_array($request->integer('limit'), [10, 15, 25, 50], true) ? $request->integer('limit') : 10;
        $companies = Company::query()
            ->visibleTo($request->user())
            ->when($request->q, fn ($q, $value) => $q->where(fn ($query) => $query->where('name', 'ilike', "%{$value}%")->orWhere('code', 'ilike', "%{$value}%")))
            ->when($entityType, fn ($q) => $q->where('entity_type', $entityType))
            ->when($classification, fn ($q) => $q->where('classification', $classification))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($roles, fn ($q) => $q->where(function ($roleQuery) use ($roles): void { foreach ($roles as $role) $roleQuery->orWhereJsonContains('roles', $role); }))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->appends($request->except('partial'));

        if ($request->boolean('partial')) {
            return response()->json([
                'head' => view('modules.company._company-table-head', compact('sort', 'direction'))->render(),
                'body' => view('modules.company._company-table-body', compact('companies'))->render(),
                'pagination' => view('modules.company._company-pagination', compact('companies'))->render(),
            ]);
        }

        return view('modules.company.companies', compact('companies', 'sort', 'direction', 'entityType', 'classification', 'status', 'roles', 'perPage'));
    }

    public function create(): View { Gate::authorize('create', Company::class); return view('modules.company.form', ['company' => new Company(), 'hasVendorRelations' => false, 'parentCompanies' => $this->parentCompanies(), 'operationalCompanies' => $this->operationalCompanies(), 'action' => route('workspace.company.store'), 'method' => 'POST']); }
    public function store(CompanyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Company::class);
        $data = $request->validated();
        $data['roles'] = $data['entity_type'] === 'group' ? ['group'] : array_values($data['roles'] ?? []);
        $visibilityCompanyIds = $data['visibility_company_ids'] ?? [];
        $logo = $data['logo'] ?? null;
        unset($data['logo']);
        unset($data['visibility_company_ids']);
        $company = Company::create($data);
        $company->operationalVisibility()->sync($this->visibilityIds($company, $visibilityCompanyIds));
        $this->storeLogo($company, $logo);

        return redirect()->route('workspace.company.show', $company)->with('status', 'Perusahaan berhasil dibuat.');
    }
    public function show(Company $company): View
    {
        Gate::authorize('view', $company);
        $company->load(['parent', 'subsidiaries', 'operationalVisibility']);

        return view('modules.company.show', compact('company'));
    }
    public function logo(Company $company): StreamedResponse
    {
        Gate::authorize('view', $company);
        abort_unless($company->logo_disk && $company->logo_object_key, 404);

        $stream = Storage::disk($company->logo_disk)->readStream($company->logo_object_key);
        abort_unless(is_resource($stream), 404);

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $company->logo_mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="company-logo-'.$company->getKey().'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
    public function edit(Company $company): View { Gate::authorize('update', $company); return view('modules.company.form', ['company' => $company, 'hasVendorRelations' => $this->hasVendorRelations($company), 'parentCompanies' => $this->parentCompanies($company), 'operationalCompanies' => $this->operationalCompanies($company), 'action' => route('workspace.company.update', $company), 'method' => 'PUT']); }
    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        Gate::authorize('update', $company);
        $data = $request->validated();
        $data['roles'] = $data['entity_type'] === 'group' ? ['group'] : array_values($data['roles'] ?? []);
        $visibilityCompanyIds = $data['visibility_company_ids'] ?? [];
        $logo = $data['logo'] ?? null;
        unset($data['logo']);
        unset($data['visibility_company_ids']);
        $company->update($data);
        $company->operationalVisibility()->sync($this->visibilityIds($company, $visibilityCompanyIds));
        $this->storeLogo($company, $logo);

        return redirect()->route('workspace.company.show', $company)->with('status', 'Perusahaan berhasil diperbarui.');
    }
    public function destroy(Company $company): RedirectResponse { Gate::authorize('delete', $company); $company->delete(); return redirect()->route('workspace.company.companies')->with('status', 'Perusahaan dipindahkan ke arsip.'); }
    private function operationalCompanies(?Company $company = null)
    {
        return Company::query()
            ->where('entity_type', 'company')
            ->where('classification', 'internal')
            ->where('status', 'active')
            ->whereJsonContains('roles', 'operating_company')
            ->when($company, fn ($query) => $query->where('id', '!=', $company->getKey()))
            ->orderBy('name')
            ->get();
    }

    private function visibilityIds(Company $company, array $ids): array
    {
        if ($company->entity_type !== 'company') {
            return [];
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    private function storeLogo(Company $company, mixed $logo): void
    {
        if (! $logo) {
            return;
        }

        $disk = config('filesystems.default', 's3');
        $extension = strtolower($logo->extension() ?: 'bin');
        $objectKey = 'app-company/'.$company->storage_key.'/brand_'.Str::ulid().'.'.$extension;
        $stored = Storage::disk($disk)->putFileAs(dirname($objectKey), $logo, basename($objectKey), [
            'ContentType' => $logo->getMimeType() ?: 'application/octet-stream',
            'visibility' => 'private',
        ]);

        if (! $stored) {
            throw new \RuntimeException('Logo perusahaan gagal disimpan ke object storage.');
        }

        $oldDisk = $company->logo_disk;
        $oldObjectKey = $company->logo_object_key;
        $company->update([
            'logo_disk' => $disk,
            'logo_object_key' => $objectKey,
            'logo_mime_type' => $logo->getMimeType(),
            'logo_file_size' => $logo->getSize(),
        ]);

        if ($oldDisk && $oldObjectKey && ($oldDisk !== $disk || $oldObjectKey !== $objectKey)) {
            Storage::disk($oldDisk)->delete($oldObjectKey);
        }
    }

    private function hasVendorRelations(Company $company): bool
    {
        return Vendor::query()->where('vendor_company_id', $company->getKey())->exists();
    }

    private function parentCompanies(?Company $company = null)
    {
        return Company::query()->where('entity_type', 'group')->where('status', 'active')->when($company, fn ($query) => $query->where('id', '!=', $company->getKey()))->orderBy('name')->get();
    }
}

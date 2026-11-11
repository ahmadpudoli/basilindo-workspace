<?php

namespace App\Modules\Integration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Integration\Application\Jobs\RunOdooReferenceSync;
use App\Modules\Integration\Application\Services\OdooPostgresDataSource;
use App\Modules\Integration\Domain\Models\IntegrationSyncRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Core\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class IntegrationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_integration');
        $allowedSorts = ['created_at', 'sync_type', 'status', 'processed_count', 'created_count', 'updated_count', 'failed_count'];
        $sort = in_array($request->query('sort'), $allowedSorts, true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $runs = IntegrationSyncRun::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = trim((string) $request->query('q'));
                $query->where(function ($nested) use ($term): void {
                    $nested->where('message', 'ilike', "%{$term}%")->orWhere('source_system', 'ilike', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('sync_type'), fn ($query) => $query->where('sync_type', $request->query('sync_type')))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
        return view('modules.integration.index', ['runs' => $runs, 'recordCounts' => collect(['res_company', 'res_partner', 'crm_lead', 'project_project'])->mapWithKeys(fn ($type) => [$type => DB::connection('core')->table('integration_reference_records')->where('entity_type', $type)->count()])]);
    }

    public function companyMapping(): View
    {
        Gate::authorize('manage_integration');
        $sourceDatabase = (string) config('database.connections.odoo.database');
        $workspaceCompanies = Company::query()->where('status', 'active')->orderBy('name')->get();
        $companies = DB::connection('core')->table('integration_reference_records as records')
            ->leftJoin('external_references as refs', function ($join) use ($sourceDatabase): void {
                $join->on('refs.external_id', '=', 'records.external_id')->where('refs.source_system', 'odoo')->where('refs.source_database', $sourceDatabase)->where('refs.entity_type', 'res_company');
            })
            ->leftJoin('companies', function ($join): void { $join->whereRaw('core_companies.id::text = core_refs.local_id')->where('refs.local_entity_type', 'company'); })
            ->where('records.entity_type', 'res_company')
            ->select(['records.external_id', 'records.display_name', 'records.payload', 'refs.local_id', 'companies.name as local_name', 'companies.code as local_code'])
            ->orderBy('records.display_name')->get();
        $companies = $companies->map(function ($company) use ($workspaceCompanies) {
            $decoded = json_decode((string) $company->display_name, true);
            $company->display_name = is_array($decoded) ? (string) ($decoded['en_US'] ?? reset($decoded) ?: 'Tanpa nama') : $company->display_name;
            $candidates = $workspaceCompanies->filter(fn (Company $workspaceCompany): bool => $this->normalizeCompanyName($workspaceCompany->name) === $this->normalizeCompanyName($company->display_name));
            $company->candidate_id = ! $company->local_id && $candidates->count() === 1 ? (string) $candidates->first()->id : null;
            $company->candidate_code = $company->candidate_id ? $candidates->first()->code : null;
            return $company;
        });
        return view('modules.integration.company-mapping', compact('companies', 'workspaceCompanies'));
    }

    public function storeCompanyMapping(Request $request): RedirectResponse
    {
        Gate::authorize('manage_integration');
        $data = $request->validate(['external_id' => ['required', 'string', 'max:120'], 'company_id' => ['required', 'integer', 'exists:core.companies,id']]);
        $sourceDatabase = (string) config('database.connections.odoo.database');
        abort_unless(DB::connection('core')->table('integration_reference_records')->where(['source_system' => 'odoo', 'source_database' => $sourceDatabase, 'entity_type' => 'res_company', 'external_id' => $data['external_id']])->exists(), 422, 'Perusahaan Odoo belum tersinkron.');
        $alreadyMapped = DB::connection('core')->table('external_references')->where(['source_system' => 'odoo', 'source_database' => $sourceDatabase, 'entity_type' => 'res_company', 'local_entity_type' => 'company', 'local_id' => (string) $data['company_id']])->where('external_id', '!=', $data['external_id'])->exists();
        abort_if($alreadyMapped, 422, 'Perusahaan Workspace tersebut sudah dipetakan ke perusahaan Odoo lain.');
        DB::connection('core')->table('external_references')->updateOrInsert(
            ['source_system' => 'odoo', 'source_database' => $sourceDatabase, 'entity_type' => 'res_company', 'external_id' => $data['external_id']],
            ['local_entity_type' => 'company', 'local_id' => (string) $data['company_id'], 'last_synced_at' => now(), 'sync_status' => 'mapped', 'updated_at' => now(), 'created_at' => now()],
        );
        return back()->with('status', 'Mapping perusahaan berhasil disimpan.');
    }

    private function normalizeCompanyName(?string $name): string
    {
        return trim((string) preg_replace('/\s+/', ' ', mb_strtolower((string) $name)));
    }

    public function testConnection(OdooPostgresDataSource $source): RedirectResponse
    {
        Gate::authorize('manage_integration');
        try { $source->testConnection(); return back()->with('status', 'Koneksi Odoo berhasil diuji.'); }
        catch (Throwable $exception) { return back()->withErrors(['connection' => 'Koneksi Odoo gagal: '.$exception->getMessage()]); }
    }

    public function sync(): RedirectResponse
    {
        Gate::authorize('run_integration_sync');
        RunOdooReferenceSync::dispatch(false);
        return back()->with('status', 'Sinkronisasi Odoo masuk antrean.');
    }

    public function initialSync(): RedirectResponse
    {
        Gate::authorize('run_integration_sync');
        RunOdooReferenceSync::dispatch(true);
        return back()->with('status', 'Initial sync Odoo masuk antrean.');
    }
}

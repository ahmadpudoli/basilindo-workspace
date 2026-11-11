<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Models\Company;
use Core\Models\NumberingRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class NumberingRuleController extends Controller
{
    private function formCatalog(): array { return ['hris_overtime' => 'Pengajuan Lembur', 'hris_leave' => 'Pengajuan Cuti', 'hris_claim' => 'Pengajuan Claim', 'hris_employee_document' => 'Dokumen Karyawan']; }
    private function authorizeManage(Request $request): void
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('manage_numbering_rules'), 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeManage($request);
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        $catalog = $this->formCatalog();
        $filters = $request->validate(['company_ids' => ['nullable', 'array'], 'company_ids.*' => ['integer'], 'form_keys' => ['nullable', 'array'], 'form_keys.*' => ['string', 'in:'.implode(',', array_keys($catalog))]]);
        $companyIds = collect($filters['company_ids'] ?? [])->map(fn ($id): int => (int) $id)->intersect($companies->pluck('id'))->values();
        $formKeys = collect($filters['form_keys'] ?? [])->filter(fn (string $key): bool => array_key_exists($key, $catalog))->values();
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['company_id', 'form_key', 'format', 'counter_scope', 'is_active'], true) ? $request->string('sort')->toString() : 'company_id';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $rules = NumberingRule::query()->whereIn('company_id', $companies->pluck('id'))->with('company')->when($request->filled('q'), fn ($q) => $q->where(fn ($nested) => $nested->where('name', 'ilike', '%'.trim($request->q).'%')->orWhere('form_key', 'ilike', '%'.trim($request->q).'%')->orWhere('format', 'ilike', '%'.trim($request->q).'%')))->when($companyIds->isNotEmpty(), fn ($q) => $q->whereIn('company_id', $companyIds))->when($formKeys->isNotEmpty(), fn ($q) => $q->whereIn('form_key', $formKeys))->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        $formCatalog = $catalog;
        $editRule = $request->filled('edit') ? NumberingRule::query()->whereIn('company_id', $companies->pluck('id'))->find($request->integer('edit')) : null;
        return view('modules.core.numbering-rules-v3', compact('rules', 'companies', 'formCatalog', 'perPage', 'sort', 'direction', 'editRule', 'companyIds', 'formKeys'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage($request);
        $catalog = $this->formCatalog();
        $data = $request->validate(['company_id' => ['required', 'integer'], 'form_key' => ['required', 'in:'.implode(',', array_keys($catalog))], 'format' => ['required', 'string', 'max:255'], 'counter_scope' => ['required', 'in:global,yearly,monthly,daily']]);
        $data['name'] = $catalog[$data['form_key']];
        abort_unless(Company::query()->operationalFor($request->user())->whereKey($data['company_id'])->exists(), 403);
        if (NumberingRule::query()->where('company_id', $data['company_id'])->where('form_key', $data['form_key'])->exists()) {
            return back()->withInput()->withErrors(['form_key' => 'Jenis form tersebut sudah memiliki master penomoran untuk perusahaan ini. Gunakan tombol Edit pada tabel.']);
        }
        NumberingRule::create([...$data, 'is_active' => true]);
        return back()->with('status', 'Master penomoran berhasil disimpan.');
    }

    public function update(Request $request, NumberingRule $numberingRule): RedirectResponse
    {
        $this->authorizeManage($request);
        $data = $request->validate(['name' => ['nullable', 'string', 'max:150'], 'format' => ['required', 'string', 'max:255'], 'counter_scope' => ['required', 'in:global,yearly,monthly,daily'], 'is_active' => ['nullable', 'boolean']]);
        abort_unless(Company::query()->operationalFor($request->user())->whereKey($numberingRule->company_id)->exists(), 403);
        $values = ['format' => $data['format'], 'counter_scope' => $data['counter_scope'], 'updated_at' => now()];
        if (array_key_exists('name', $data) && filled($data['name'])) $values['name'] = $data['name'];
        if ($request->has('is_active')) $values['is_active'] = $request->boolean('is_active');
        DB::connection('core')->table('numbering_rules')->where('id', $numberingRule->getKey())->update($values);
        return redirect()->route('workspace.core.numbering-rules')->with('status', 'Master penomoran berhasil diperbarui. Format: '.$data['format']);
    }
}

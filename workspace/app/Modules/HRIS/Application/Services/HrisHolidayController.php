<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\HRIS\Domain\Models\HrisHoliday;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use Core\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HrisHolidayController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', HrisHoliday::class);
        $activeYear = (int) HrisSetting::resolve(null, 'hris.active_year', now()->year);
        $year = $request->integer('year') ?: $activeYear;
        $type = in_array($request->string('type')->toString(), ['national', 'company', 'collective_leave'], true) ? $request->string('type')->toString() : null;
        $status = $request->string('status')->toString() === 'archived' ? 'archived' : 'active';
        $perPage = in_array($request->integer('limit'), [10, 25, 50, 100], true) ? $request->integer('limit') : 10;
        $sort = in_array($request->string('sort')->toString(), ['holiday_date', 'name', 'type', 'created_at'], true) ? $request->string('sort')->toString() : 'holiday_date';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $holidays = HrisHoliday::query()->visibleTo($request->user())
            ->when($status === 'active', fn ($query) => $query->where('is_active', true)->whereNull('deleted_at'))
            ->when($status === 'archived', fn ($query) => $query->withTrashed()->where(function ($archived): void { $archived->where('is_active', false)->orWhereNotNull('deleted_at'); }))
            ->whereYear('holiday_date', $year)
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'ilike', '%'.$request->string('q')->toString().'%'))
            ->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        HrisHoliday::loadCompanies($holidays->getCollection());

        return view('modules.hris-holidays', [
            'holidays' => $holidays,
            'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->get(['id', 'name', 'code']),
            'year' => $year,
            'type' => $type,
            'status' => $status,
            'perPage' => $perPage,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', HrisHoliday::class);
        return view('modules.hris-holiday-form', [
            'holiday' => new HrisHoliday(['holiday_date' => HrisSetting::resolve(null, 'hris.active_year', now()->year).'-01-01', 'type' => 'national']),
            'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->get(['id', 'name', 'code']),
            'selectedCompanyIds' => [],
            'canGlobal' => $request->user()->hasRole('super_admin'),
        ]);
    }

    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        Gate::authorize('create', HrisHoliday::class);
        [$data, $companyIds] = $this->validated($request);
        $data['created_by'] = $request->user()->getKey();
        $holiday = HrisHoliday::create($data);
        $this->syncCompanyIds($holiday, $companyIds);
        $audit->record('hris.holiday.created', $holiday, null, ['company_ids' => $companyIds, 'holiday_type' => $holiday->type]);

        return redirect()->route('workspace.hris.holidays')->with('status', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Request $request, HrisHoliday $holiday): View
    {
        Gate::authorize('update', $holiday);
        return view('modules.hris-holiday-form', [
            'holiday' => $holiday,
            'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->get(['id', 'name', 'code']),
            'selectedCompanyIds' => $holiday->companyIds()->map(fn ($id): int => (int) $id)->all(),
            'canGlobal' => $request->user()->hasRole('super_admin'),
        ]);
    }

    public function update(Request $request, HrisHoliday $holiday, AuditLogger $audit): RedirectResponse
    {
        Gate::authorize('update', $holiday);
        [$data, $companyIds] = $this->validated($request, $holiday);
        $before = $holiday->only(['holiday_date', 'name', 'type', 'description', 'deduct_annual_leave', 'is_global', 'is_active']);
        $holiday->update($data);
        $this->syncCompanyIds($holiday, $companyIds);
        $audit->recordChange('hris.holiday.updated', $holiday, $before, $holiday->fresh()->only(array_keys($before)), null, ['company_ids' => $companyIds]);

        return redirect()->route('workspace.hris.holidays')->with('status', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(HrisHoliday $holiday, AuditLogger $audit): RedirectResponse
    {
        Gate::authorize('delete', $holiday);
        $before = $holiday->only(['holiday_date', 'name', 'type', 'deduct_annual_leave', 'is_global']);
        $holiday->update(['is_active' => false]);
        $holiday->delete();
        $audit->recordChange('hris.holiday.archived', $holiday, $before, ['is_active' => false, 'deleted_at' => now()->toIso8601String()]);

        return back()->with('status', 'Hari libur dipindahkan ke arsip.');
    }

    private function validated(Request $request, ?HrisHoliday $holiday = null): array
    {
        $data = $request->validate([
            'holiday_date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:national,company,collective_leave'],
            'description' => ['nullable', 'string', 'max:2000'],
            'deduct_annual_leave' => ['nullable', 'boolean'],
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => ['integer', 'exists:core.companies,id'],
        ]);

        $companyIds = collect($data['company_ids'] ?? [])->map(fn ($id): int => (int) $id)->unique()->values()->all();
        $allowedCompanyIds = Company::query()->operationalFor($request->user())->pluck('id')->map(fn ($id): int => (int) $id)->all();
        abort_unless(count(array_diff($companyIds, $allowedCompanyIds)) === 0, 403);
        if ($companyIds === [] && ! $request->user()->hasRole('super_admin')) {
            throw \Illuminate\Validation\ValidationException::withMessages(['company_ids' => 'Pilih minimal satu perusahaan. Cakupan global hanya dapat digunakan oleh super admin.']);
        }
        if ($data['type'] === 'collective_leave' && ! $request->boolean('deduct_annual_leave')) {
            throw \Illuminate\Validation\ValidationException::withMessages(['deduct_annual_leave' => 'Cuti bersama wajib mengurangi kuota cuti tahunan.']);
        }

        $duplicate = HrisHoliday::query()->whereDate('holiday_date', $data['holiday_date'])->where('name', $data['name'])->when($holiday, fn ($query) => $query->where($holiday->getKeyName(), '!=', $holiday->getKey()))->where('is_active', true)->exists();
        if ($duplicate) {
            throw \Illuminate\Validation\ValidationException::withMessages(['name' => 'Hari libur dengan tanggal dan nama yang sama sudah tersedia.']);
        }

        unset($data['company_ids']);
        $data['is_global'] = $companyIds === [];
        $data['deduct_annual_leave'] = $data['type'] === 'collective_leave' ? true : $request->boolean('deduct_annual_leave');
        $data['is_active'] = true;
        return [$data, $companyIds];
    }

    private function syncCompanyIds(HrisHoliday $holiday, array $companyIds): void
    {
        $pivot = DB::connection('hris')->table('holiday_companies');
        $pivot->where('holiday_id', $holiday->getKey())->delete();

        if ($companyIds !== []) {
            $pivot->insert(array_map(
                fn (int $companyId): array => ['holiday_id' => $holiday->getKey(), 'company_id' => $companyId],
                $companyIds,
            ));
        }
    }
}

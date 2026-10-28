<?php

namespace App\Modules\HRIS\Application\Services;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Domain\Models\HrisSetting;
use App\Modules\HRIS\Domain\Models\HrisWorkflowApprover;
use Carbon\CarbonImmutable;
use Core\Models\Company;
use Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HrisSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeSettings($request);
        $perPage = in_array((int) $request->integer('per_page', 10), [10, 25, 50, 100], true) ? (int) $request->integer('per_page', 10) : 10;
        $sort = in_array($request->string('sort')->toString(), ['company_id', 'key', 'effective_from', 'is_active', 'created_at'], true) ? $request->string('sort')->toString() : 'effective_from';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $status = $request->string('status', 'active')->toString();
        $settings = $this->visibleSettings($request)->when($request->filled('q'), fn ($query) => $query->where('key', 'ilike', '%'.$request->string('q')->toString().'%'))
            ->when($status === 'active', fn ($query) => $query->whereNull('archived_at'))
            ->when($status === 'archived', fn ($query) => $query->whereNotNull('archived_at'))
            ->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        return view('modules.hris-settings-index', ['settings' => $settings, 'companies' => $companies, 'users' => User::query()->orderBy('name')->get(), 'workflowApprovers' => HrisWorkflowApprover::with(['company', 'user'])->whereIn('company_id', $companies->pluck('id'))->where('workflow_key', 'leave_sick')->where('step', 'hr')->where('is_active', true)->orderBy('company_id')->get(), 'editing' => null, 'perPage' => $perPage]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeSettings($request);
        $data = $this->validated($request);
        $normalized = $this->normalized($request, $data);
        $this->ensureUniqueCutoffYear($normalized);
        HrisSetting::create($normalized);
        return back()->with('status', 'Parameter HRIS berhasil disimpan.');
    }

    public function workflow(Request $request): View
    {
        $this->authorizeSettings($request);
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        $availableCompanies = $this->availableWorkflowCompanies($companies);
        return view('modules.hris-workflow-settings-paginated', [
            'companies' => $companies,
            'availableCompanies' => $availableCompanies,
            'users' => User::query()->orderBy('name')->get(),
            'workflowApprovers' => $this->workflowQuery($request, $companies)->paginate($this->workflowPerPage($request))->withQueryString(),
            'perPage' => $this->workflowPerPage($request),
            'filter' => $request->string('workflow')->toString(),
            'search' => $request->string('q')->toString(),
            'approverFilter' => $request->integer('approver_id') ?: null,
            'editingApprover' => null,
        ]);
    }

    public function workflowEdit(Request $request, HrisWorkflowApprover $approver): View
    {
        $this->authorizeSettings($request);
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        abort_unless($approver->is_active && $companies->contains('id', $approver->company_id), 404);
        $availableCompanies = $this->availableWorkflowCompanies($companies);
        $editingCompany = $companies->firstWhere('id', $approver->company_id);
        if ($editingCompany) $availableCompanies[$approver->workflow_key] = $availableCompanies[$approver->workflow_key]->push($editingCompany)->sortBy('name')->unique('id')->values();
        return view('modules.hris-workflow-settings-paginated', [
            'companies' => $companies,
            'availableCompanies' => $availableCompanies,
            'users' => User::query()->orderBy('name')->get(),
            'workflowApprovers' => $this->workflowQuery($request, $companies)->paginate($this->workflowPerPage($request))->withQueryString(),
            'perPage' => $this->workflowPerPage($request),
            'filter' => $request->string('workflow')->toString(),
            'search' => $request->string('q')->toString(),
            'approverFilter' => $request->integer('approver_id') ?: null,
            'editingApprover' => $approver,
        ]);
    }

    public function edit(Request $request, HrisSetting $setting): View
    {
        $this->authorizeSettings($request);
        abort_unless($this->visibleSettings($request)->whereKey($setting->getKey())->exists(), 404);
        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get();
        return view('modules.hris-settings-index', ['settings' => $this->visibleSettings($request)->whereKey($setting->getKey())->paginate(10), 'companies' => $companies, 'users' => User::query()->orderBy('name')->get(), 'workflowApprovers' => collect(), 'editing' => $setting, 'perPage' => 10]);
    }

    public function update(Request $request, HrisSetting $setting): RedirectResponse
    {
        $this->authorizeSettings($request);
        abort_unless($this->visibleSettings($request)->whereKey($setting->getKey())->exists(), 404);
        $normalized = $this->normalized($request, $this->validated($request), false);
        $this->ensureUniqueCutoffYear($normalized, $setting);
        $setting->update($normalized);
        return redirect()->route('workspace.settings.hris')->with('status', 'Parameter HRIS berhasil diperbarui.');
    }

    public function archive(Request $request, HrisSetting $setting): RedirectResponse
    {
        $this->authorizeSettings($request);
        abort_unless($this->visibleSettings($request)->whereKey($setting->getKey())->exists(), 404);
        $setting->update(['is_active' => false, 'archived_at' => now(), 'archived_by' => $request->user()->getKey()]);
        return back()->with('status', 'Parameter HRIS berhasil diarsipkan.');
    }

    public function restore(Request $request, HrisSetting $setting): RedirectResponse
    {
        $this->authorizeSettings($request);
        abort_unless($this->visibleSettings($request)->whereKey($setting->getKey())->exists(), 404);
        $this->ensureUniqueCutoffYear([
            'key' => $setting->key,
            'value' => $setting->value,
        ], $setting);
        $setting->update(['is_active' => true, 'archived_at' => null, 'archived_by' => null]);
        return back()->with('status', 'Parameter HRIS berhasil dipulihkan.');
    }

    public function workflowStore(Request $request): RedirectResponse
    {
        $this->authorizeSettings($request);
        $data = $request->validate([
            'company_id' => ['required', 'integer'],
            'workflow_key' => ['nullable', 'in:leave_sick,claim'],
            'user_id' => ['required', 'integer'],
        ]);
        abort_unless(User::query()->whereKey($data['user_id'])->exists(), 422, 'Approver yang dipilih tidak ditemukan.');
        $data['workflow_key'] = $data['workflow_key'] ?? 'leave_sick';
        $step = $data['workflow_key'] === 'claim' ? 'finance' : 'hr';
        abort_unless(Company::query()->operationalFor($request->user())->whereKey($data['company_id'])->exists(), 403);
        if (HrisWorkflowApprover::query()->where('company_id', $data['company_id'])->where('workflow_key', $data['workflow_key'])->where('step', $step)->exists()) {
            return back()->withInput()->withErrors(['company_id' => 'Perusahaan ini sudah memiliki konfigurasi workflow untuk jenis tersebut. Gunakan tombol Edit pada tabel.']);
        }
        $archived = HrisWorkflowApprover::withTrashed()
            ->where('company_id', $data['company_id'])
            ->where('workflow_key', $data['workflow_key'])
            ->where('step', $step)
            ->whereNotNull('deleted_at')
            ->latest('deleted_at')
            ->first();

        if ($archived) {
            $archived->restore();
            $archived->update(['user_id' => $data['user_id'], 'is_active' => true]);
        } else {
            HrisWorkflowApprover::create(['company_id' => $data['company_id'], 'workflow_key' => $data['workflow_key'], 'step' => $step, 'user_id' => $data['user_id'], 'is_active' => true]);
        }
        return back()->with('status', 'Workflow approval berhasil disimpan.');
    }

    public function workflowUpdate(Request $request, HrisWorkflowApprover $approver): RedirectResponse
    {
        $this->authorizeSettings($request);
        $data = $request->validate(['company_id' => ['required', 'integer'], 'user_id' => ['required', 'integer']]);
        abort_unless(User::query()->whereKey($data['user_id'])->exists(), 422, 'Approver yang dipilih tidak ditemukan.');
        $allowed = Company::query()->operationalFor($request->user())->whereKey($data['company_id'])->exists();
        abort_unless($allowed && Company::query()->operationalFor($request->user())->whereKey($approver->company_id)->exists() && $approver->is_active, 403);
        $duplicate = HrisWorkflowApprover::query()->where('company_id', $data['company_id'])->where('workflow_key', $approver->workflow_key)->where('step', $approver->step)->where($approver->getKeyName(), '!=', $approver->getKey())->exists();
        if ($duplicate) return back()->withInput()->withErrors(['company_id' => 'Perusahaan ini sudah memiliki konfigurasi workflow untuk jenis tersebut.']);
        $approver->update(['company_id' => $data['company_id'], 'user_id' => $data['user_id']]);
        return redirect()->route('workspace.settings.hris.workflow')->with('status', 'Workflow approval berhasil diperbarui.');
    }

    public function workflowArchive(Request $request, HrisWorkflowApprover $approver): RedirectResponse
    {
        $this->authorizeSettings($request);
        abort_unless($approver->is_active && Company::query()->operationalFor($request->user())->whereKey($approver->company_id)->exists(), 403);
        $approver->delete();
        return back()->with('status', 'Workflow approval berhasil diarsipkan.');
    }

    private function authorizeSettings(Request $request): void
    {
        abort_unless($request->user()->hasRole('super_admin') || $request->user()->can('manage_hris_settings'), 403);
    }

    private function visibleSettings(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        return HrisSetting::query()->when(! $request->user()->hasRole('super_admin'), fn ($q) => $q->whereIn('company_id', $request->user()->companies()->pluck('companies.id')));
    }

    private function workflowPerPage(Request $request): int
    {
        $value = (int) $request->integer('per_page', 10);
        return in_array($value, [10, 25, 50, 100], true) ? $value : 10;
    }

    private function workflowQuery(Request $request, $companies): \Illuminate\Database\Eloquent\Builder
    {
        $sort = in_array($request->string('sort')->toString(), ['company_id', 'workflow_key', 'user_id', 'created_at'], true) ? $request->string('sort')->toString() : 'company_id';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        return HrisWorkflowApprover::with(['company', 'user'])->whereIn('company_id', $companies->pluck('id'))->whereIn('workflow_key', ['leave_sick', 'claim'])->where('is_active', true)
            ->when($request->filled('workflow'), fn ($query) => $query->where('workflow_key', $request->string('workflow')->toString()))
            ->when($request->filled('approver_id'), fn ($query) => $query->where('user_id', $request->integer('approver_id')))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($nested) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';
                $nested->whereHas('company', fn ($company) => $company->where('name', 'ilike', $term))->orWhereHas('user', fn ($user) => $user->where('name', 'ilike', $term)->orWhere('email', 'ilike', $term));
            }))
            ->orderBy($sort, $direction);
    }

    private function availableWorkflowCompanies($companies): array
    {
        return collect(['leave_sick', 'claim'])->mapWithKeys(function (string $workflowKey) use ($companies): array {
            $configuredIds = HrisWorkflowApprover::query()->where('workflow_key', $workflowKey)->where('is_active', true)->pluck('company_id');
            return [$workflowKey => $companies->whereNotIn('id', $configuredIds)->values()];
        })->all();
    }

    private function validated(Request $request): array
    {
        return $request->validate(['company_id' => ['nullable', 'integer'], 'key' => ['required', 'in:hris.annual_leave_quota,hris.leave_submission_cutoff,hris.overtime_maximum_minutes,hris.claim_final_approval_threshold'], 'value' => ['required', 'string', 'max:1000'], 'effective_from' => ['nullable', 'date']]);
    }

    private function normalized(Request $request, array $data, bool $create = true): array
    {
        if ($data['company_id'] && ! $request->user()->hasRole('super_admin')) abort_unless($request->user()->companies()->whereKey($data['company_id'])->exists(), 403);
        $value = match ($data['key']) {
            'hris.annual_leave_quota', 'hris.overtime_maximum_minutes', 'hris.claim_final_approval_threshold' => is_numeric($data['value']) ? (float) $data['value'] : $this->throwValidationError('value', 'Nilai harus numerik.'),
            'hris.leave_submission_cutoff' => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $data['value']) && strtotime((string) $data['value']) !== false ? $data['value'] : $this->throwValidationError('value', 'Cut-off harus menggunakan format tanggal YYYY-MM-DD.'),
            default => $data['value'],
        };
        return ['company_id' => $data['company_id'] ?? null, 'key' => $data['key'], 'value' => $value, 'effective_from' => $data['effective_from'] ?? null] + ($create ? ['is_active' => true] : []);
    }

    private function throwValidationError(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    /**
     * A cut-off is a yearly policy. It must not be configured twice for the
     * same year, even when one record is global and the other is company-specific.
     */
    private function ensureUniqueCutoffYear(array $data, ?HrisSetting $ignore = null): void
    {
        if (($data['key'] ?? null) !== 'hris.leave_submission_cutoff') {
            return;
        }

        $value = $data['value'] ?? null;
        $value = is_array($value) ? ($value['date'] ?? $value['value'] ?? null) : $value;
        $cutoff = is_string($value) ? CarbonImmutable::createFromFormat('!Y-m-d', $value) : false;

        if (! $cutoff || CarbonImmutable::createFromFormat('!Y-m-d', $value)->format('Y-m-d') !== $value) {
            return;
        }

        $year = (int) $cutoff->format('Y');
        $duplicate = HrisSetting::query()
            ->where('key', 'hris.leave_submission_cutoff')
            ->where('is_active', true)
            ->whereNull('archived_at')
            ->when($ignore, fn ($query) => $query->where($ignore->getKeyName(), '!=', $ignore->getKey()))
            ->get()
            ->first(function (HrisSetting $setting) use ($year): bool {
                $existing = $setting->value;
                $existing = is_array($existing) ? ($existing['date'] ?? $existing['value'] ?? null) : $existing;

                return is_string($existing)
                    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $existing) === 1
                    && (int) substr($existing, 0, 4) === $year;
            });

        if ($duplicate) {
            throw ValidationException::withMessages([
                'key' => "Cut-off pengajuan untuk tahun {$year} sudah dikonfigurasi. Satu tahun hanya boleh memiliki satu konfigurasi cut-off aktif.",
            ]);
        }
    }
}

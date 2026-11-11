<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Domain\Models\CrmAccount;
use App\Modules\CRM\Domain\Models\CrmContact;
use App\Modules\CRM\Domain\Models\CrmActivity;
use App\Modules\CRM\Domain\Models\CrmLead;
use App\Modules\CRM\Domain\Models\CrmOpportunity;
use App\Modules\CRM\Domain\Models\CrmPipelineStage;
use App\Modules\CRM\Domain\Models\CrmClosingStatus;
use Core\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CrmController extends Controller
{
    private const RESOURCES = [
        'accounts' => [CrmAccount::class, 'Account', 'CrmAccountPolicy'],
        'contacts' => [CrmContact::class, 'Contact', 'CrmContactPolicy'],
        'leads' => [CrmLead::class, 'Lead', 'CrmLeadPolicy'],
        'opportunities' => [CrmOpportunity::class, 'Opportunity', 'CrmOpportunityPolicy'],
        'activities' => [CrmActivity::class, 'Aktivitas', 'CrmActivityPolicy'],
    ];

    public function index(Request $request, string $resource): View
    {
        [$model, $label] = $this->resource($resource);
        Gate::authorize('viewAny', $model);
        $perPage = in_array($request->integer('per_page', 10), [10, 25, 50, 100], true)
            ? $request->integer('per_page', 10)
            : 10;

        $sort = $resource === 'accounts' && in_array($request->string('sort')->toString(), ['name', 'account_type', 'created_at'], true)
            ? $request->string('sort')->toString()
            : 'name';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $query = $model::query();

        if ($resource === 'accounts') {
            $accountTypes = collect((array) $request->input('account_type', []))
                ->filter(fn ($value) => in_array($value, ['company', 'person'], true))
                ->values();
            $ownerCompanyIds = collect((array) $request->input('owner_company_id', []))
                ->filter(fn ($value) => is_numeric($value))
                ->map(fn ($value) => (int) $value)
                ->values();
            $partyCompanyIds = collect((array) $request->input('party_company_id', []))
                ->filter(fn ($value) => is_numeric($value))
                ->map(fn ($value) => (int) $value)
                ->values();

            $query->with(['contacts', 'company', 'partyCompany'])
                ->when($request->filled('q'), fn ($query) => $query->where('name', 'ilike', '%'.$request->string('q')->toString().'%'))
                ->when($accountTypes->isNotEmpty(), fn ($query) => $query->whereIn('account_type', $accountTypes->all()))
                ->when($ownerCompanyIds->isNotEmpty(), fn ($query) => $query->whereIn('company_id', $ownerCompanyIds->all()))
                ->when($partyCompanyIds->isNotEmpty(), fn ($query) => $query->whereIn('party_company_id', $partyCompanyIds->all()))
                ->orderBy($sort, $direction);

            if (! $request->user()->hasRole('super_admin')) {
                $query->whereIn('company_id', Company::query()->operationalFor($request->user())->select('id'));
            }
        } else {
            $query->when($request->q, function ($query, $value) use ($resource) {
                $column = $resource === 'activities' ? 'subject' : 'name';
                $query->where($column, 'ilike', "%{$value}%");
            })->latest();
        }

        return view($resource === 'accounts' ? 'modules.crm.account-index' : 'modules.crm.resource-index', [
            'resource' => $resource,
            'label' => $label,
            'records' => $query->paginate($perPage)->withQueryString(),
            'columns' => $this->columns($resource),
            'perPage' => $perPage,
            'sort' => $sort,
            'direction' => $direction,
            'ownerCompanies' => $resource === 'accounts'
                ? Company::query()->operationalFor($request->user())->orderBy('name')->get(['id', 'name'])
                : collect(),
            'partyCompanies' => $resource === 'accounts'
                ? Company::query()->where(function ($query) {
                    $query->whereJsonContains('roles', 'client')
                        ->orWhereJsonContains('roles', 'partner');
                })->orderBy('name')->get(['id', 'name'])
                : collect(),
        ]);
    }

    public function show(string $id): View
    {
        $account = CrmAccount::query()->with(['company', 'partyCompany', 'contacts', 'opportunities', 'activities', 'documents.documentType'])->findOrFail($id);
        Gate::authorize('view', $account);
        return view('modules.crm.account-show', compact('account'));
    }

    public function create(Request $request, string $resource): View
    {
        [$model, $label] = $this->resource($resource);
        Gate::authorize('create', $model);
        return view('modules.crm.resource-form', array_merge(compact('resource', 'label'), $this->options($resource, $request->user())));
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        [$model, $label] = $this->resource($resource); Gate::authorize('create', $model); $data = $this->validated($request, $resource); if ($resource === 'opportunities') $data = $this->normalizeOpportunity($data); $record = $model::create($data);
        return $request->string('return_to')->toString() === 'pipeline'
            ? redirect()->route('workspace.crm.pipeline')->with('status', "$label berhasil dibuat.")
            : redirect()->route('workspace.crm.resource', $resource)->with('status', "$label berhasil dibuat.");
    }

    public function edit(Request $request, string $resource, string $id): View
    {
        [$model, $label] = $this->resource($resource);
        $record = $model::query()->findOrFail($id);
        Gate::authorize('update', $record);
        return view('modules.crm.resource-form', array_merge(compact('resource', 'label', 'record'), $this->options($resource, $request->user())));
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse { [$model, $label] = $this->resource($resource); $record = $model::query()->findOrFail($id); Gate::authorize('update', $record); $data = $this->validated($request, $resource); if ($resource === 'opportunities') $data = $this->normalizeOpportunity($data); $data = $this->localUpdateData($record, $resource, $data); $record->update($data); return redirect()->route('workspace.crm.resource', $resource)->with('status', "$label berhasil diperbarui."); }

    public function destroy(string $resource, string $id): RedirectResponse { [$model, $label] = $this->resource($resource); $record = $model::query()->findOrFail($id); Gate::authorize('delete', $record); $record->delete(); return back()->with('status', "$label dihapus."); }

    private function resource(string $resource): array { abort_unless(isset(self::RESOURCES[$resource]), 404); return [self::RESOURCES[$resource][0], self::RESOURCES[$resource][1]]; }
    private function columns(string $resource): array
    {
        return match ($resource) {
            'accounts' => ['name', 'industry', 'segment'],
            'contacts' => ['name', 'email', 'phone', 'job_title'],
            'leads' => ['name', 'email', 'status', 'source'],
            'opportunities' => ['name', 'stage', 'amount', 'currency'],
            default => ['subject', 'type', 'due_at', 'completed_at'],
        };
    }

    private function options(string $resource, \Core\Models\User $user): array
    {
        return match ($resource) {
            'accounts', 'leads' => ['companies' => Company::query()->operationalFor($user)->orderBy('name')->pluck('name', 'id')],
            'contacts' => ['accounts' => CrmAccount::query()->orderBy('name')->pluck('name', 'id')],
            'opportunities' => ['accounts' => CrmAccount::query()->orderBy('name')->pluck('name', 'id'), 'stages' => CrmPipelineStage::query()->where('is_active', true)->orderBy('sequence')->get(), 'closingStatuses' => CrmClosingStatus::query()->where('is_active', true)->orderBy('sequence')->get()],
            'activities' => [
                'companies' => Company::query()->operationalFor($user)->orderBy('name')->pluck('name', 'id'),
                'accounts' => CrmAccount::query()->orderBy('name')->pluck('name', 'id'),
                'opportunities' => CrmOpportunity::query()->orderBy('name')->pluck('name', 'id'),
            ],
            default => [],
        };
    }

    private function validated(Request $request, string $resource): array
    {
        $rules = match ($resource) {
            'accounts' => ['company_id' => 'required|exists:core_companies,id', 'name' => 'required|string|max:255', 'industry' => 'nullable|string|max:100', 'segment' => 'nullable|string|max:100'],
            'contacts' => ['account_id' => 'required|exists:accounts,id', 'name' => 'required|string|max:255', 'email' => 'nullable|email|max:255', 'phone' => 'nullable|string|max:50', 'job_title' => 'nullable|string|max:150', 'is_primary' => 'nullable|boolean'],
            'leads' => ['company_id' => 'required|exists:core_companies,id', 'account_id' => 'nullable|exists:accounts,id', 'name' => 'required|string|max:255', 'email' => 'nullable|email|max:255', 'phone' => 'nullable|string|max:50', 'status' => 'nullable|string|max:50', 'source' => 'nullable|string|max:100', 'notes' => 'nullable|string'],
            'opportunities' => ['account_id' => 'required|exists:accounts,id', 'contact_id' => 'nullable|exists:contacts,id', 'team_id' => ['nullable', 'uuid', Rule::exists('crm.teams', 'id')], 'name' => 'required|string|max:255', 'stage_id' => ['nullable', 'uuid', Rule::exists('crm.pipeline_stages', 'id')], 'closing_status_id' => ['nullable', 'uuid', Rule::exists('crm.closing_statuses', 'id')], 'owner_user_id' => 'nullable|integer|exists:core_users,id', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|max:10', 'expected_close_date' => 'nullable|date', 'po_number' => 'nullable|string|max:100', 'notes' => 'nullable|string'],
            default => ['company_id' => 'required|exists:core_companies,id', 'account_id' => 'nullable|exists:accounts,id', 'opportunity_id' => 'nullable|exists:opportunities,id', 'type' => 'required|string|max:100', 'subject' => 'required|string|max:255', 'description' => 'nullable|string', 'due_at' => 'nullable|date', 'completed_at' => 'nullable|date'],
        };
        return $request->validate($rules);
    }

    private function normalizeOpportunity(array $data): array
    {
        $stage = ! empty($data['stage_id']) ? CrmPipelineStage::query()->find($data['stage_id']) : CrmPipelineStage::query()->where('code', 'new')->first();
        $data['stage_id'] = $stage?->getKey();
        $data['stage'] = $stage?->code ?: 'new';
        $data['closing_status_id'] ??= CrmClosingStatus::query()->where('code', 'open')->value('id');
        if ($stage?->is_won && empty($data['won_at'])) $data['won_at'] = now();
        return $data;
    }

    private function localUpdateData(object $record, string $resource, array $data): array
    {
        if (! ($record->is_integrated ?? false) && blank($record->source_system ?? null)) {
            return $data;
        }

        $localFields = match ($resource) {
            'accounts' => ['industry', 'segment', 'owner_user_id', 'estimated_value', 'currency'],
            'contacts' => ['job_title', 'is_primary'],
            'opportunities' => ['owner_user_id', 'po_number', 'po_document_id', 'project_id'],
            default => [],
        };

        return Arr::only($data, $localFields);
    }
}

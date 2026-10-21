<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Domain\Models\CrmAccount;
use App\Modules\CRM\Domain\Models\CrmContact;
use App\Modules\CRM\Domain\Models\CrmActivity;
use App\Modules\CRM\Domain\Models\CrmLead;
use App\Modules\CRM\Domain\Models\CrmOpportunity;
use Core\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

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
        $query = $model::query()->when($request->q, function ($query, $value) use ($resource) {
            $column = $resource === 'activities' ? 'subject' : 'name';
            $query->where($column, 'ilike', "%{$value}%");
        })->latest();

        return view('modules.crm.resource-index', ['resource' => $resource, 'label' => $label, 'records' => $query->paginate(15)->withQueryString(), 'columns' => $this->columns($resource)]);
    }

    public function show(string $id): View
    {
        $account = CrmAccount::query()->with(['company', 'contacts', 'opportunities', 'activities', 'documents.documentType'])->findOrFail($id);
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
        [$model, $label] = $this->resource($resource); Gate::authorize('create', $model); $record = $model::create($this->validated($request, $resource));
        return redirect()->route('workspace.crm.resource', $resource)->with('status', "$label berhasil dibuat.");
    }

    public function edit(Request $request, string $resource, string $id): View
    {
        [$model, $label] = $this->resource($resource);
        $record = $model::query()->findOrFail($id);
        Gate::authorize('update', $record);
        return view('modules.crm.resource-form', array_merge(compact('resource', 'label', 'record'), $this->options($resource, $request->user())));
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse { [$model, $label] = $this->resource($resource); $record = $model::query()->findOrFail($id); Gate::authorize('update', $record); $record->update($this->validated($request, $resource)); return redirect()->route('workspace.crm.resource', $resource)->with('status', "$label berhasil diperbarui."); }

    public function destroy(string $resource, string $id): RedirectResponse { [$model, $label] = $this->resource($resource); $record = $model::query()->findOrFail($id); Gate::authorize('delete', $record); $record->delete(); return back()->with('status', "$label dihapus."); }

    private function resource(string $resource): array { abort_unless(isset(self::RESOURCES[$resource]), 404); return [self::RESOURCES[$resource][0], self::RESOURCES[$resource][1]]; }
    private function columns(string $resource): array
    {
        return match ($resource) {
            'accounts' => ['name', 'status', 'industry', 'segment'],
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
            'opportunities' => ['accounts' => CrmAccount::query()->orderBy('name')->pluck('name', 'id')],
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
            'accounts' => ['company_id' => 'required|exists:core_companies,id', 'name' => 'required|string|max:255', 'status' => 'nullable|string|max:50', 'industry' => 'nullable|string|max:100', 'segment' => 'nullable|string|max:100'],
            'contacts' => ['account_id' => 'required|exists:accounts,id', 'name' => 'required|string|max:255', 'email' => 'nullable|email|max:255', 'phone' => 'nullable|string|max:50', 'job_title' => 'nullable|string|max:150', 'is_primary' => 'nullable|boolean'],
            'leads' => ['company_id' => 'required|exists:core_companies,id', 'account_id' => 'nullable|exists:accounts,id', 'name' => 'required|string|max:255', 'email' => 'nullable|email|max:255', 'phone' => 'nullable|string|max:50', 'status' => 'nullable|string|max:50', 'source' => 'nullable|string|max:100', 'notes' => 'nullable|string'],
            'opportunities' => ['account_id' => 'required|exists:accounts,id', 'name' => 'required|string|max:255', 'stage' => 'nullable|string|max:50', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|max:10', 'expected_close_date' => 'nullable|date', 'notes' => 'nullable|string'],
            default => ['company_id' => 'required|exists:core_companies,id', 'account_id' => 'nullable|exists:accounts,id', 'opportunity_id' => 'nullable|exists:opportunities,id', 'type' => 'required|string|max:100', 'subject' => 'required|string|max:255', 'description' => 'nullable|string', 'due_at' => 'nullable|date', 'completed_at' => 'nullable|date'],
        };
        return $request->validate($rules);
    }
}

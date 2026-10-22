<?php

namespace App\Modules\DocumentManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentQueryService;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentMetadataValueNormalizer;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentUploadService;
use App\Modules\DocumentManagement\Application\Services\Verification\VerificationReviewService;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentLifecycleService;
use App\Modules\DocumentManagement\Application\Services\Bundles\DocumentBundleService;
use App\Modules\DocumentManagement\Application\Jobs\GenerateDocumentBundle;
use App\Modules\DocumentManagement\Domain\Models\Bundle;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use App\Modules\DocumentManagement\Domain\Models\AuditEvent;
use App\Modules\DocumentManagement\Domain\Models\VerificationCase;
use Core\Models\Company;
use Core\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DocumentManagementController extends Controller
{
    public function typeCreate(Request $request): View { Gate::authorize('create', DocumentType::class); return view('modules.documents.type-form', ['companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id')]); }
    public function typeStore(Request $request, AuditLogger $audit): RedirectResponse { Gate::authorize('create', DocumentType::class); $data = $this->typeData($request); $companyIds = $data['company_ids']; unset($data['company_ids']); $type = DocumentType::create($data); $this->syncDocumentTypeCompanies($type, $companyIds); $audit->record('document_type.created', $type, null, ['company_ids' => $companyIds], null, $this->documentTypeAuditSnapshot($type)); return redirect()->route('workspace.documents.types.show', $type)->with('status', 'Tipe dokumen berhasil dibuat.'); }
    public function typeEdit(Request $request, DocumentType $type): View { Gate::authorize('update', $type); return view('modules.documents.type-form', ['type' => $type, 'auditEvents' => $this->documentTypeAuditEvents($type), 'selectedCompanyIds' => $this->documentTypeCompanyIds($type), 'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id')]); }
    public function typeShow(DocumentType $type): View { Gate::authorize('view', $type); return view('modules.documents.type-show', ['type' => $type->load('company'), 'auditEvents' => $this->documentTypeAuditEvents($type), 'companies' => $this->documentTypeCompanyNames($type)]); }
    public function typeUpdate(Request $request, DocumentType $type, AuditLogger $audit): RedirectResponse { Gate::authorize('update', $type); $before = $this->documentTypeAuditSnapshot($type); $data = $this->typeData($request); $companyIds = $data['company_ids']; unset($data['company_ids']); $type->update($data); $this->syncDocumentTypeCompanies($type, $companyIds); $type->refresh(); $audit->recordChange('document_type.updated', $type, $before, $this->documentTypeAuditSnapshot($type), null, ['company_ids' => $companyIds]); return redirect()->route('workspace.documents.types.show', $type)->with('status', 'Tipe dokumen berhasil diperbarui.'); }
    public function typeDestroy(DocumentType $type, AuditLogger $audit): RedirectResponse
    {
        Gate::authorize('delete', $type);
        if ($type->documents()->exists()) {
            return back()->with('error', 'Tipe dokumen tidak dapat dihapus karena sudah digunakan pada upload dokumen.');
        }

        $before = $this->documentTypeAuditSnapshot($type);
        $type->delete();
        $audit->recordChange('document_type.archived', $type, $before, ['deleted_at' => now()->toIso8601String()]);

        return back()->with('status', 'Tipe dokumen dihapus.');
    }

    private function typeData(Request $request): array
    {
        $data = $request->validate(['company_ids' => 'nullable|array', 'company_ids.*' => 'integer|exists:core.companies,id', 'name' => 'required|string|max:255', 'code' => 'required|string|max:100', 'required_fields' => 'nullable|json', 'is_active' => 'nullable|boolean']);
        $data['company_ids'] = array_values(array_unique(array_map('intval', $data['company_ids'] ?? [])));
        $data['company_id'] = $data['company_ids'][0] ?? null;
        $data['required_fields'] = filled($data['required_fields'] ?? null) ? json_decode($data['required_fields'], true, 512, JSON_THROW_ON_ERROR) : [];
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
    private function documentTypeAuditEvents(DocumentType $type)
    {
        return AuditEvent::query()->with('actor')->where('subject_type', $type->getMorphClass())->where('subject_id', $type->getKey())->latest('created_at')->limit(30)->get();
    }

    private function documentTypeAuditSnapshot(DocumentType $type): array
    {
        return [
            'id' => $type->getKey(),
            'name' => $type->name,
            'code' => $type->code,
            'company_id' => $type->company_id,
            'company_ids' => $this->documentTypeCompanyIds($type),
            'required_fields' => $type->required_fields,
            'is_active' => $type->is_active,
        ];
    }

    private function documentTypeCompanyNames(DocumentType $type)
    {
        return Company::query()->whereIn('id', $this->documentTypeCompanyIds($type))->orderBy('name')->pluck('name', 'id');
    }
    public function show(Document $document): View { Gate::authorize('view', $document); $auditEvents = AuditEvent::query()->with('actor')->where('subject_type', $document->getMorphClass())->where('subject_id', $document->getKey())->latest('created_at')->limit(30)->get(); return view('modules.documents.show', ['document' => $document->load(['company', 'counterpartyCompany', 'project', 'documentType', 'currentVersion']), 'auditEvents' => $auditEvents]); }
    public function edit(Request $request, Document $document): View { Gate::authorize('update', $document); $linkedTypeIds = DB::connection('documents')->table('document_type_companies')->where('company_id', $document->company_id)->pluck('document_type_id'); $auditEvents = AuditEvent::query()->with('actor')->where('subject_type', $document->getMorphClass())->where('subject_id', $document->getKey())->latest('created_at')->limit(30)->get(); return view('modules.documents.edit', ['document' => $document->load(['company', 'documentType']), 'auditEvents' => $auditEvents, 'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id'), 'counterpartyCompanies' => $this->counterpartyCompanies($request), 'projects' => Project::query()->orderBy('name')->pluck('name', 'id'), 'types' => DocumentType::query()->where('is_active', true)->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $document->company_id)->orWhereIn('id', $linkedTypeIds))->orderBy('name')->get()]); }

    public function update(Request $request, Document $document, AuditLogger $audit): RedirectResponse
    { Gate::authorize('update', $document); $before = $this->auditSnapshot($document); $this->normalizeAmountInput($request); $data = $request->validate(['company_id' => 'required|exists:core.companies,id', 'counterparty_company_id' => 'nullable|exists:core.companies,id', 'project_id' => 'nullable|exists:core.projects,id', 'document_type_id' => 'required|exists:documents.document_types,id', 'title' => 'required|string|max:255', 'description' => 'nullable|string', 'document_date' => 'required|date', 'reference_number' => 'nullable|string|max:255', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|max:10', 'tags' => 'nullable|string', 'metadata' => 'nullable|array']); $this->ensureOperationalCompanyAllowed($data['company_id'], $request); $this->ensureCounterpartyCompanyAllowed($data['counterparty_company_id'] ?? null, $request); $this->ensureDocumentTypeAppliesToCompany($data['document_type_id'], $data['company_id']); $data['metadata'] = $this->validatedMetadata($data['metadata'] ?? [], DocumentType::findOrFail($data['document_type_id'])); $data['tags'] = filled($data['tags'] ?? null) ? array_values(array_filter(array_map('trim', explode(',', $data['tags'])))) : []; $document->update($data); $document->refresh(); $audit->recordChange('document.updated', $document, $before, $this->auditSnapshot($document), $document->company_id); return redirect()->route('workspace.documents.show', $document)->with('status', 'Dokumen berhasil diperbarui.'); }
    public function bundleCreate(Request $request): View { Gate::authorize('create', Bundle::class); return view('modules.documents.bundle-form', ['companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id')]); }

    public function bundleStore(Request $request, DocumentBundleService $bundleService): RedirectResponse
    {
        Gate::authorize('create', Bundle::class);
        $data = $request->validate(['company_id' => 'required|exists:core.companies,id', 'q' => 'nullable|string|max:255', 'status' => 'nullable|string|max:50', 'year' => 'nullable|integer|min:2000|max:2100', 'from' => 'nullable|date', 'until' => 'nullable|date|after_or_equal:from']);
        $bundle = $bundleService->createFromFilters($data, $request->user());
        GenerateDocumentBundle::dispatch((string) $bundle->getKey());
        return redirect()->route('workspace.documents.bundles')->with('status', 'Request bundel masuk antrean queue.');
    }

    public function destroy(Document $document, AuditLogger $audit): RedirectResponse { Gate::authorize('delete', $document); $before = $this->auditSnapshot($document); $document->delete(); $audit->recordChange('document.archived', $document, $before, ['deleted_at' => now()->toIso8601String()], $document->company_id); return redirect()->route('workspace.documents.list')->with('status', 'Dokumen dipindahkan ke quarantine/arsip melalui soft delete.'); }
    public function verificationCase(VerificationCase $case): View { Gate::authorize('viewAny', Document::class); return view('modules.documents.verification-case', ['case' => $case->load(['items.document'])]); }

    public function review(Request $request, VerificationReviewService $reviewService, \App\Modules\DocumentManagement\Domain\Models\VerificationItem $item): RedirectResponse
    { $data = $request->validate(['result' => 'required|in:passed,failed,waived', 'comment' => 'nullable|string|max:2000']); $reviewService->review($item->load('verificationCase'), $request->user(), $data['result'], $data['comment'] ?? null); return back()->with('status', 'Hasil review berhasil disimpan.'); }

    public function release(Request $request, DocumentLifecycleService $lifecycle, Document $document): RedirectResponse { Gate::authorize('review', $document); $lifecycle->release($document, (int) $request->user()->getKey()); return back()->with('status', 'Dokumen berhasil dirilis.'); }
    public function reject(Request $request, DocumentLifecycleService $lifecycle, Document $document): RedirectResponse { Gate::authorize('review', $document); $data = $request->validate(['reason' => 'required|string|max:2000']); $lifecycle->reject($document, (int) $request->user()->getKey(), $data['reason']); return back()->with('status', 'Dokumen ditolak.'); }
    public function create(Request $request): View { Gate::authorize('create', Document::class); return view('modules.documents.form', ['companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id'), 'counterpartyCompanies' => $this->counterpartyCompanies($request), 'projects' => Project::query()->orderBy('name')->pluck('name', 'id'), 'types' => DocumentType::query()->where('is_active', true)->orderBy('name')->get(), 'defaults' => $request->only(['project_id', 'company_id'])]); }

    public function store(Request $request, DocumentUploadService $uploadService): RedirectResponse
    {
        Gate::authorize('create', Document::class);
        $this->normalizeAmountInput($request);
        $data = $request->validate(['upload' => 'required|file|mimes:pdf,jpg,jpeg,png|max:51200', 'company_id' => 'required|exists:core.companies,id', 'counterparty_company_id' => 'nullable|exists:core.companies,id', 'project_id' => 'nullable|exists:core.projects,id', 'document_type_id' => 'required|exists:documents.document_types,id', 'title' => 'required|string|max:255', 'description' => 'nullable|string', 'document_date' => 'required|date', 'reference_number' => 'nullable|string|max:255', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|max:10', 'tags' => 'nullable|string', 'metadata' => 'nullable|array']);
        $this->ensureOperationalCompanyAllowed($data['company_id'], $request);
        $this->ensureCounterpartyCompanyAllowed($data['counterparty_company_id'] ?? null, $request);
        $this->ensureDocumentTypeAppliesToCompany($data['document_type_id'], $data['company_id']);
        $data['metadata'] = $this->validatedMetadata($data['metadata'] ?? [], DocumentType::findOrFail($data['document_type_id']));
        $data['tags'] = filled($data['tags'] ?? null) ? array_values(array_filter(array_map('trim', explode(',', $data['tags']))) ) : [];
        $file = $data['upload']; unset($data['upload']);
        $document = $uploadService->upload($file, $data, (int) $request->user()->getKey());
        return redirect()->route('workspace.documents.show', $document)->with('status', "Dokumen {$document->title} berhasil diunggah dan tersedia.");
    }
    public function documents(Request $request, DocumentQueryService $queryService): View
    {
        Gate::authorize('viewAny', Document::class);
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'company_ids' => array_values(array_unique(array_map('intval', array_filter((array) $request->input('company_ids', []), 'is_numeric')))),
            'year' => $request->integer('year') ?: null,
        ];
        $perPage = $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;
        $operationalCompanies = Company::query()
            ->operationalFor($request->user())
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
        $allowedCompanyIds = $operationalCompanies->modelKeys();
        $filters['company_ids'] = array_values(array_intersect($filters['company_ids'], array_map('intval', $allowedCompanyIds)));

        $visibleReadyDocuments = (clone $queryService->visibleTo($request->user()))->where('status', 'ready');
        $availableYears = $visibleReadyDocuments
            ->whereNotNull('document_date')
            ->selectRaw('EXTRACT(YEAR FROM document_date)::int AS year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn (mixed $year): int => (int) $year)
            ->all();

        $query = $queryService->apply($queryService->visibleTo($request->user()), $filters)
            ->where('status', 'ready')
            ->with(['company', 'documentType'])
            ->latest();

        return view('modules.documents.documents', ['documents' => $query->paginate($perPage)->withQueryString(), 'filters' => $filters, 'operationalCompanies' => $operationalCompanies, 'availableYears' => $availableYears, 'perPage' => $perPage]);
    }

    private function counterpartyCompanies(Request $request)
    {
        return Company::query()
            ->visibleTo($request->user())
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereJsonContains('roles', 'client')->orWhereJsonContains('roles', 'vendor'))
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'roles']);
    }

    private function ensureCounterpartyCompanyAllowed(mixed $companyId, Request $request): void
    {
        if (blank($companyId)) {
            return;
        }

        $allowed = Company::query()
            ->visibleTo($request->user())
            ->whereKey($companyId)
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereJsonContains('roles', 'client')->orWhereJsonContains('roles', 'vendor'))
            ->exists();

        if (! $allowed) {
            throw ValidationException::withMessages([
                'counterparty_company_id' => 'Perusahaan client/vendor yang dipilih tidak tersedia dalam scope akses Anda.',
            ]);
        }
    }

    private function ensureOperationalCompanyAllowed(int|string $companyId, Request $request): void
    {
        if (! Company::query()->operationalFor($request->user())->whereKey($companyId)->exists()) {
            throw ValidationException::withMessages([
                'company_id' => 'Perusahaan operasional yang dipilih tidak tersedia dalam scope akses Anda.',
            ]);
        }
    }

    private function normalizeAmountInput(Request $request): void
    {
        $request->merge([
            'amount' => DocumentMetadataValueNormalizer::numeric($request->input('amount'), 'numeric_decimal'),
        ]);
    }

    private function validatedMetadata(array $metadata, DocumentType $type): array
    {
        $rules = [];
        foreach ($type->metadataDefinitions() as $field => $config) {
            if (in_array($config['type'], ['numeric', 'numeric_decimal'], true) && array_key_exists($field, $metadata)) {
                $metadata[$field] = DocumentMetadataValueNormalizer::numeric($metadata[$field], $config['type']);
            }
            if ($config['type'] === 'boolean' && array_key_exists($field, $metadata)) {
                $normalizedBoolean = filter_var($metadata[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($normalizedBoolean !== null) {
                    $metadata[$field] = $normalizedBoolean;
                }
            }

            $typeRule = match ($config['type']) {
                'numeric' => 'integer',
                'numeric_decimal' => 'numeric',
                'date', 'datetime' => 'date',
                'boolean' => 'boolean',
                default => 'string',
            };
            $rules[$field] = ($config['mandatory'] ? 'required' : 'nullable').'|'.$typeRule;
        }

        return Validator::make($metadata, $rules)->validate();
    }

    private function ensureDocumentTypeAppliesToCompany(string|int $typeId, string|int $companyId): void
    {
        $linkedTypeIds = DB::connection('documents')->table('document_type_companies')->where('company_id', $companyId)->pluck('document_type_id');
        $valid = DocumentType::query()
            ->whereKey($typeId)
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $companyId)->orWhereIn('id', $linkedTypeIds))
            ->exists();

        if (! $valid) {
            throw ValidationException::withMessages([
                'document_type_id' => 'Jenis dokumen harus aktif dan berlaku untuk perusahaan internal yang dipilih.',
            ]);
        }
    }

    private function documentTypeCompanyIds(DocumentType $type): array
    {
        $ids = DB::connection('documents')->table('document_type_companies')->where('document_type_id', $type->getKey())->pluck('company_id')->map(fn ($id): string => (string) $id)->all();

        return $ids ?: ($type->company_id ? [(string) $type->company_id] : []);
    }

    private function syncDocumentTypeCompanies(DocumentType $type, array $companyIds): void
    {
        $connection = DB::connection('documents');
        $connection->table('document_type_companies')->where('document_type_id', $type->getKey())->delete();

        if ($companyIds !== []) {
            $now = now();
            $connection->table('document_type_companies')->insert(array_map(fn (int $companyId): array => [
                'document_type_id' => $type->getKey(),
                'company_id' => $companyId,
                'created_at' => $now,
                'updated_at' => $now,
            ], $companyIds));
        }
    }

    public function types(Request $request): View
    {
        Gate::authorize('viewAny', Document::class);
        $allowedSorts = ['name', 'code', 'is_active', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->input('limit', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;
        $search = trim((string) $request->input('q', ''));
        $status = in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : '';
        $companyIds = array_values(array_unique(array_map('intval', array_filter((array) $request->input('company_ids', []), 'is_numeric'))));
        $companyIds = Company::query()->operationalFor($request->user())->whereIn('id', $companyIds)->pluck('id')->map(fn ($id): int => (int) $id)->all();

        $query = DocumentType::query()->with('company')->withCount('documents');
        if ($search !== '') {
            $matchingCompanyIds = Company::query()
                ->where(fn ($builder) => $builder->where('name', 'ilike', "%{$search}%")->orWhere('code', 'ilike', "%{$search}%"))
                ->pluck('id');
            $matchingLinkedTypeIds = $matchingCompanyIds->isEmpty()
                ? collect()
                : DB::connection('documents')->table('document_type_companies')->whereIn('company_id', $matchingCompanyIds)->pluck('document_type_id');

            $query->where(fn ($builder) => $builder
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('code', 'ilike', "%{$search}%")
                ->orWhereIn('company_id', $matchingCompanyIds)
                ->orWhereIn('id', $matchingLinkedTypeIds));
        }
        if ($status !== '') {
            $query->where('is_active', $status === 'active');
        }
        if ($companyIds !== []) {
            $matchingLinkedTypeIds = DB::connection('documents')->table('document_type_companies')->whereIn('company_id', $companyIds)->pluck('document_type_id');
            $query->where(fn ($builder) => $builder->whereIn('company_id', $companyIds)->orWhereIn('id', $matchingLinkedTypeIds));
        }

        $types = $query->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
        $typeCompanyIds = DB::connection('documents')->table('document_type_companies')->whereIn('document_type_id', $types->getCollection()->pluck('id'))->get()->groupBy('document_type_id');
        $companyNames = Company::query()->whereIn('id', $typeCompanyIds->flatten()->pluck('company_id')->unique())->pluck('name', 'id');

        $companies = Company::query()->operationalFor($request->user())->orderBy('name')->get(['id', 'name', 'code']);

        return view('modules.documents.types', compact('types', 'typeCompanyIds', 'companyNames', 'companies', 'companyIds', 'search', 'status', 'sort', 'direction', 'perPage'));
    }

    public function verification(Request $request): View
    {
        Gate::authorize('viewAny', Document::class);
        $cases = VerificationCase::query()->latest()->paginate(15)->withQueryString();

        return view('modules.documents.verification', compact('cases'));
    }

    public function bundles(Request $request): View
    {
        Gate::authorize('viewAny', Document::class);
        $bundles = Bundle::query()->with('company')->latest()->paginate(15)->withQueryString();

        return view('modules.documents.bundles', compact('bundles'));
    }

    private function auditSnapshot(Document $document): array
    {
        return collect($document->only([
            'id', 'company_id', 'counterparty_company_id', 'project_id', 'document_type_id',
            'title', 'description', 'original_filename', 'reference_number', 'document_date',
            'amount', 'currency', 'status', 'metadata', 'tags', 'legal_hold', 'retention_until',
            'verified_at', 'quarantined_at', 'released_at', 'rejected_at', 'deleted_at',
        ]))->map(function (mixed $value): mixed {
            if ($value instanceof \DateTimeInterface) {
                return $value->format(DATE_ATOM);
            }

            return $value;
        })->all();
    }
}

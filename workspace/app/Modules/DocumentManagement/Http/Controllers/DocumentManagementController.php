<?php

namespace App\Modules\DocumentManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentQueryService;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentUploadService;
use App\Modules\DocumentManagement\Application\Services\Verification\VerificationReviewService;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentLifecycleService;
use App\Modules\DocumentManagement\Application\Services\Bundles\DocumentBundleService;
use App\Modules\DocumentManagement\Application\Jobs\GenerateDocumentBundle;
use App\Modules\DocumentManagement\Domain\Models\Bundle;
use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Domain\Models\DocumentType;
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
    public function typeStore(Request $request): RedirectResponse { Gate::authorize('create', DocumentType::class); $data = $this->typeData($request); $companyIds = $data['company_ids']; unset($data['company_ids']); $type = DocumentType::create($data); $this->syncDocumentTypeCompanies($type, $companyIds); return redirect()->route('workspace.documents.types')->with('status', 'Tipe dokumen berhasil dibuat.'); }
    public function typeEdit(Request $request, DocumentType $type): View { Gate::authorize('update', $type); return view('modules.documents.type-form', ['type' => $type, 'selectedCompanyIds' => $this->documentTypeCompanyIds($type), 'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id')]); }
    public function typeUpdate(Request $request, DocumentType $type): RedirectResponse { Gate::authorize('update', $type); $data = $this->typeData($request); $companyIds = $data['company_ids']; unset($data['company_ids']); $type->update($data); $this->syncDocumentTypeCompanies($type, $companyIds); return redirect()->route('workspace.documents.types')->with('status', 'Tipe dokumen berhasil diperbarui.'); }
    public function typeDestroy(DocumentType $type): RedirectResponse { Gate::authorize('delete', $type); abort_if($type->documents()->exists(), 422, 'Tipe dokumen yang masih dipakai tidak dapat dihapus.'); $type->delete(); return back()->with('status', 'Tipe dokumen dihapus.'); }

    private function typeData(Request $request): array
    {
        $data = $request->validate(['company_ids' => 'nullable|array', 'company_ids.*' => 'integer|exists:core.companies,id', 'name' => 'required|string|max:255', 'code' => 'required|string|max:100', 'required_fields' => 'nullable|json', 'is_active' => 'nullable|boolean']);
        $data['company_ids'] = array_values(array_unique(array_map('intval', $data['company_ids'] ?? [])));
        $data['company_id'] = $data['company_ids'][0] ?? null;
        $data['required_fields'] = filled($data['required_fields'] ?? null) ? json_decode($data['required_fields'], true, 512, JSON_THROW_ON_ERROR) : [];
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
    public function edit(Request $request, Document $document): View { Gate::authorize('update', $document); $linkedTypeIds = DB::connection('documents')->table('document_type_companies')->where('company_id', $document->company_id)->pluck('document_type_id'); return view('modules.documents.edit', ['document' => $document->load(['company', 'documentType']), 'companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id'), 'projects' => Project::query()->orderBy('name')->pluck('name', 'id'), 'types' => DocumentType::query()->where('is_active', true)->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $document->company_id)->orWhereIn('id', $linkedTypeIds))->orderBy('name')->get()]); }

    public function update(Request $request, Document $document): RedirectResponse
    { Gate::authorize('update', $document); $data = $request->validate(['company_id' => 'required|exists:core_companies,id', 'counterparty_company_id' => 'nullable|exists:core_companies,id', 'project_id' => 'nullable|exists:core_projects,id', 'document_type_id' => 'required|exists:document_types,id', 'title' => 'required|string|max:255', 'description' => 'nullable|string', 'document_date' => 'required|date', 'reference_number' => 'nullable|string|max:255', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|max:10', 'tags' => 'nullable|string', 'metadata' => 'nullable|array']); $this->ensureDocumentTypeAppliesToCompany($data['document_type_id'], $data['company_id']); $data['metadata'] = $this->validatedMetadata($data['metadata'] ?? [], DocumentType::findOrFail($data['document_type_id'])); $data['tags'] = filled($data['tags'] ?? null) ? array_values(array_filter(array_map('trim', explode(',', $data['tags'])))) : []; $document->update($data); return redirect()->route('workspace.documents.list')->with('status', 'Metadata dokumen berhasil diperbarui.'); }
    public function bundleCreate(Request $request): View { Gate::authorize('create', Bundle::class); return view('modules.documents.bundle-form', ['companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id')]); }

    public function bundleStore(Request $request, DocumentBundleService $bundleService): RedirectResponse
    {
        Gate::authorize('create', Bundle::class);
        $data = $request->validate(['company_id' => 'required|exists:core_companies,id', 'q' => 'nullable|string|max:255', 'status' => 'nullable|string|max:50', 'year' => 'nullable|integer|min:2000|max:2100', 'from' => 'nullable|date', 'until' => 'nullable|date|after_or_equal:from']);
        $bundle = $bundleService->createFromFilters($data, $request->user());
        GenerateDocumentBundle::dispatch((string) $bundle->getKey());
        return redirect()->route('workspace.documents.bundles')->with('status', 'Request bundel masuk antrean queue.');
    }

    public function destroy(Document $document): RedirectResponse { Gate::authorize('delete', $document); $document->delete(); return redirect()->route('workspace.documents.list')->with('status', 'Dokumen dipindahkan ke quarantine/arsip melalui soft delete.'); }
    public function verificationCase(VerificationCase $case): View { Gate::authorize('viewAny', Document::class); return view('modules.documents.verification-case', ['case' => $case->load(['items.document'])]); }

    public function review(Request $request, VerificationReviewService $reviewService, \App\Modules\DocumentManagement\Domain\Models\VerificationItem $item): RedirectResponse
    { $data = $request->validate(['result' => 'required|in:passed,failed,waived', 'comment' => 'nullable|string|max:2000']); $reviewService->review($item->load('verificationCase'), $request->user(), $data['result'], $data['comment'] ?? null); return back()->with('status', 'Hasil review berhasil disimpan.'); }

    public function release(Request $request, DocumentLifecycleService $lifecycle, Document $document): RedirectResponse { Gate::authorize('review', $document); $lifecycle->release($document, (int) $request->user()->getKey()); return back()->with('status', 'Dokumen berhasil dirilis.'); }
    public function reject(Request $request, DocumentLifecycleService $lifecycle, Document $document): RedirectResponse { Gate::authorize('review', $document); $data = $request->validate(['reason' => 'required|string|max:2000']); $lifecycle->reject($document, (int) $request->user()->getKey(), $data['reason']); return back()->with('status', 'Dokumen ditolak.'); }
    public function create(Request $request): View { Gate::authorize('create', Document::class); return view('modules.documents.form', ['companies' => Company::query()->operationalFor($request->user())->orderBy('name')->pluck('name', 'id'), 'projects' => Project::query()->orderBy('name')->pluck('name', 'id'), 'types' => DocumentType::query()->where('is_active', true)->orderBy('name')->get(), 'defaults' => $request->only(['project_id', 'crm_account_id', 'crm_opportunity_id', 'company_id'])]); }

    public function store(Request $request, DocumentUploadService $uploadService): RedirectResponse
    {
        Gate::authorize('create', Document::class);
        $data = $request->validate(['upload' => 'required|file|mimes:pdf,jpg,jpeg,png|max:51200', 'company_id' => 'required|exists:core_companies,id', 'counterparty_company_id' => 'nullable|exists:core_companies,id', 'project_id' => 'nullable|exists:core_projects,id', 'crm_account_id' => 'nullable|exists:accounts,id', 'crm_opportunity_id' => 'nullable|exists:opportunities,id', 'document_type_id' => 'required|exists:document_types,id', 'title' => 'required|string|max:255', 'description' => 'nullable|string', 'document_date' => 'required|date', 'reference_number' => 'nullable|string|max:255', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|max:10', 'tags' => 'nullable|string', 'metadata' => 'nullable|array']);
        $this->ensureDocumentTypeAppliesToCompany($data['document_type_id'], $data['company_id']);
        $data['metadata'] = $this->validatedMetadata($data['metadata'] ?? [], DocumentType::findOrFail($data['document_type_id']));
        $data['tags'] = filled($data['tags'] ?? null) ? array_values(array_filter(array_map('trim', explode(',', $data['tags']))) ) : [];
        $file = $data['upload']; unset($data['upload']);
        $document = $uploadService->upload($file, $data, (int) $request->user()->getKey());
        return redirect()->route('workspace.documents.list')->with('status', "Dokumen {$document->title} berhasil diunggah dan masuk quarantine.");
    }
    public function documents(Request $request, DocumentQueryService $queryService): View
    {
        Gate::authorize('viewAny', Document::class);
        $query = $queryService->apply($queryService->visibleTo($request->user()), $request->only(['q', 'status', 'year', 'from', 'until']))
            ->with(['company', 'documentType'])
            ->latest();

        return view('modules.documents.documents', ['documents' => $query->paginate(15)->withQueryString(), 'filters' => $request->only(['q', 'status', 'year', 'from', 'until'])]);
    }

    private function validatedMetadata(array $metadata, DocumentType $type): array
    {
        $rules = [];
        foreach ($type->metadataDefinitions() as $field => $config) {
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
        $types = DocumentType::query()->with('company')->withCount('documents')->latest()->paginate(15)->withQueryString();
        $typeCompanyIds = DB::connection('documents')->table('document_type_companies')->whereIn('document_type_id', $types->getCollection()->pluck('id'))->get()->groupBy('document_type_id');
        $companyNames = Company::query()->whereIn('id', $typeCompanyIds->flatten()->pluck('company_id')->unique())->pluck('name', 'id');

        return view('modules.documents.types', compact('types', 'typeCompanyIds', 'companyNames'));
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
}

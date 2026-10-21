<?php

namespace App\Modules\DocumentManagement\Livewire;

use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Core\Models\Company;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class DocumentMetadataFields extends Component
{
    public array $types = [];
    public ?string $companyId = null;
    public ?string $documentTypeId = null;
    public array $metadata = [];

    public function mount(iterable $types, string|int|null $selected = null, array $initial = [], string|int|null $companyId = null): void
    {
        $this->companyId = $companyId === null ? null : (string) $companyId;
        $this->types = $this->companyId ? $this->typesForCompany() : [];
        $this->documentTypeId = $selected === null ? null : (string) $selected;
        $this->metadata = $initial;
    }

    #[On('document-company-changed')]
    public function companyChanged(string|int|null $companyId = null): void
    {
        $this->companyId = filled($companyId) ? (string) $companyId : null;
        $this->documentTypeId = null;
        $this->metadata = [];
        $this->types = $this->companyId ? $this->typesForCompany() : [];
    }

    public function updatedDocumentTypeId(): void { $this->metadata = []; }

    private function typesForCompany(): array
    {
        if (! $this->companyId || ! auth()->check()) {
            return [];
        }

        $companyAllowed = Company::query()->operationalFor(auth()->user())->whereKey($this->companyId)->exists();
        if (! $companyAllowed) {
            return [];
        }

        $linkedTypeIds = DB::connection('documents')
            ->table('document_type_companies')
            ->where('company_id', $this->companyId)
            ->pluck('document_type_id');

        return DocumentType::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereNull('company_id')
                ->orWhere('company_id', $this->companyId)
                ->orWhereIn('id', $linkedTypeIds))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (DocumentType $type): array => [(string) $type->getKey() => (string) $type->name])
            ->all();
    }

    public function render()
    {
        $type = $this->documentTypeId ? DocumentType::query()->find($this->documentTypeId) : null;
        return view('livewire.document-metadata-fields', ['definitions' => $type?->metadataDefinitions() ?? []]);
    }
}

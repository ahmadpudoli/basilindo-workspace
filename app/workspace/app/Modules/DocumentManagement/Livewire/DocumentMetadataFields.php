<?php

namespace App\Modules\DocumentManagement\Livewire;

use App\Modules\DocumentManagement\Domain\Models\DocumentType;
use Livewire\Component;

class DocumentMetadataFields extends Component
{
    public array $types = [];
    public ?string $documentTypeId = null;
    public array $metadata = [];

    public function mount(iterable $types, string|int|null $selected = null, array $initial = []): void
    {
        $this->types = collect($types)->mapWithKeys(function ($type): array {
            if (is_array($type)) {
                return [(string) $type['id'] => (string) $type['name']];
            }

            return [(string) $type->getKey() => (string) $type->name];
        })->all();
        $this->documentTypeId = $selected === null ? null : (string) $selected;
        $this->metadata = $initial;
    }

    public function updatedDocumentTypeId(): void { $this->metadata = []; }

    public function render()
    {
        $type = $this->documentTypeId ? DocumentType::query()->find($this->documentTypeId) : null;
        return view('livewire.document-metadata-fields', ['definitions' => $type?->metadataDefinitions() ?? []]);
    }
}

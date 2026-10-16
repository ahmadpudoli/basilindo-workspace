<?php

namespace App\Filament\Resources\DocumentTypes\Pages;

use App\Filament\Resources\DocumentTypes\DocumentTypeResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditDocumentType extends EditRecord
{
    protected static string $resource = DocumentTypeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['metadata_rows'] = DocumentTypeResource::metadataRows($this->record->required_fields);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $requiredFields = DocumentTypeResource::metadataMap($data['metadata_rows'] ?? []);

        if ($this->record->documents()->exists()) {
            $errors = [];

            if (($data['company_id'] ?? null) != $this->record->company_id) {
                $errors['data.company_id'] = sprintf(
                    'Perusahaan jenis dokumen tidak dapat diubah karena sudah digunakan oleh %d dokumen. Buat jenis dokumen baru untuk perusahaan lain.',
                    $this->record->documents()->count(),
                );
            }

            $currentFields = $this->record->metadataDefinitions();
            $newFields = $requiredFields;
            ksort($currentFields);
            ksort($newFields);

            if ($currentFields !== $newFields) {
                $errors['data.metadata_rows'] = sprintf(
                    'Metadata tidak dapat diubah atau dihapus karena sudah digunakan oleh %d dokumen. Buat jenis dokumen baru dengan metadata yang berbeda.',
                    $this->record->documents()->count(),
                );
            }

            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        }

        $data['required_fields'] = $requiredFields;
        unset($data['metadata_rows']);

        return $data;
    }
}

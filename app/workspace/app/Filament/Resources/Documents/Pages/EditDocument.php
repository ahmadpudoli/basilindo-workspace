<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\DocumentType;
use App\Services\Documents\DocumentMetadataValueNormalizer;
use Core\Models\Project;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    public function getFormContentComponent(): Component
    {
        return parent::getFormContentComponent()
            ->extraAttributes(['novalidate' => true]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['upload']);

        if (filled($data['amount'] ?? null)) {
            $amount = DocumentMetadataValueNormalizer::numeric($data['amount'], 'numeric_decimal');

            if (! DocumentMetadataValueNormalizer::isValidNumeric($amount, 'numeric_decimal') || (float) $amount < 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal harus berupa angka desimal yang valid dan tidak boleh negatif.',
                ]);
            }

            $data['amount'] = $amount;
        } else {
            $data['amount'] = null;
        }

        if (filled($data['project_id'] ?? null) && ! Project::query()
            ->whereKey($data['project_id'])
            ->where('company_id', $data['company_id'] ?? null)
            ->exists()) {
            throw ValidationException::withMessages([
                'project_id' => 'Proyek yang dipilih harus berasal dari perusahaan internal yang dipilih.',
            ]);
        }

        if (! DocumentType::query()
            ->whereKey($data['document_type_id'] ?? null)
            ->where('is_active', true)
            ->where(function ($query) use ($data): void {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $data['company_id'] ?? null);
            })
            ->exists()) {
            throw ValidationException::withMessages([
                'document_type_id' => 'Jenis dokumen harus berstatus aktif dan berlaku untuk perusahaan internal yang dipilih.',
            ]);
        }

        return $data;
    }
}

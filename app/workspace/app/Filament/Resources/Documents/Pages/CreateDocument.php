<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Services\Documents\DocumentUploadService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $file = $data['upload'] ?? null;
        unset($data['upload']);

        if (! $file) {
            throw new \InvalidArgumentException('File dokumen wajib diunggah.');
        }

        return app(DocumentUploadService::class)->upload($file, $data, (int) auth()->id());
    }
}

<?php

namespace App\Filament\Resources\SavedSearchResource\Pages;

use App\Filament\Resources\SavedSearchResource;
use Filament\Resources\Pages\EditRecord;

class EditSavedSearch extends EditRecord
{
    protected static string $resource = SavedSearchResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['filters'] = array_filter($data['filters'] ?? [], fn ($value) => $value !== null && $value !== '');

        return $data;
    }
}

<?php

namespace App\Filament\Resources\SavedSearchResource\Pages;

use App\Filament\Resources\SavedSearchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSavedSearch extends CreateRecord
{
    protected static string $resource = SavedSearchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['filters'] = array_filter($data['filters'] ?? [], fn ($value) => $value !== null && $value !== '');

        return $data;
    }
}

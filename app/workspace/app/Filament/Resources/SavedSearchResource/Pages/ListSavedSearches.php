<?php

namespace App\Filament\Resources\SavedSearchResource\Pages;

use App\Filament\Resources\SavedSearchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSavedSearches extends ListRecords
{
    protected static string $resource = SavedSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

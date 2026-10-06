<?php

namespace App\Filament\Resources\ApplicationAccesses\Pages;

use App\Filament\Resources\ApplicationAccesses\ApplicationAccessResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApplicationAccess extends EditRecord
{
    protected static string $resource = ApplicationAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->label('Hapus')];
    }
}

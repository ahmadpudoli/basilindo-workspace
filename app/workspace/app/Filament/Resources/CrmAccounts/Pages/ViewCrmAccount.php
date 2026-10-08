<?php

namespace App\Filament\Resources\CrmAccounts\Pages;

use App\Filament\Resources\CrmAccounts\CrmAccountResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewCrmAccount extends ViewRecord
{
    protected static string $resource = CrmAccountResource::class;

    protected function getHeaderActions(): array { return [EditAction::make()]; }

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi account')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('name')->label('Nama account/customer'),
                    TextEntry::make('company.name')->label('Perusahaan'),
                    TextEntry::make('status')->label('Status')->badge(),
                    TextEntry::make('industry')->label('Industri')->placeholder('Belum diatur'),
                    TextEntry::make('segment')->label('Segmen')->placeholder('Belum diatur'),
                    TextEntry::make('estimated_value')->label('Estimasi nilai')->placeholder('Belum diatur'),
                ]),
            ]),
            Section::make('Ringkasan relasi')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('contacts_count')->label('Contact')->getStateUsing(fn ($record) => $record->contacts()->count()),
                    TextEntry::make('opportunities_count')->label('Opportunity')->getStateUsing(fn ($record) => $record->opportunities()->count()),
                    TextEntry::make('activities_count')->label('Activity')->getStateUsing(fn ($record) => $record->activities()->count()),
                    TextEntry::make('documents_count')->label('Dokumen')->getStateUsing(fn ($record) => $record->documents()->count()),
                ]),
            ]),
        ]);
    }
}

<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Proyek')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('name')->label('Nama proyek'),
                    TextEntry::make('company.name')->label('Perusahaan'),
                    TextEntry::make('start_date')->label('Tanggal mulai')->date('d/m/Y')->placeholder('Belum diatur'),
                    TextEntry::make('end_date')->label('Tanggal selesai')->date('d/m/Y')->placeholder('Belum diatur'),
                ]),
                TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
            ]),
            Section::make('Ringkasan')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('members_count')->label('Anggota')->getStateUsing(fn ($record) => $record->members()->count()),
                    TextEntry::make('tickets_count')->label('Tickets')->getStateUsing(fn ($record) => $record->tickets()->count()),
                    TextEntry::make('documents_count')->label('Dokumen')->getStateUsing(fn ($record) => $record->documents()->count()),
                ]),
            ]),
        ]);
    }
}

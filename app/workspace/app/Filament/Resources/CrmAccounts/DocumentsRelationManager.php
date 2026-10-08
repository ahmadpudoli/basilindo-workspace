<?php

namespace App\Filament\Resources\CrmAccounts;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Dokumen account';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('title')->columns([
            TextColumn::make('title')->label('Judul')->searchable()->sortable(),
            TextColumn::make('documentType.name')->label('Jenis')->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('document_date')->label('Tanggal')->date('d/m/Y')->sortable(),
        ])->headerActions([
            Action::make('uploadDocument')->label('Unggah dokumen')->icon('heroicon-o-arrow-up-tray')->url(fn (): string => DocumentResource::getUrl('create', ['crm_account_id' => $this->getOwnerRecord()->getKey()])),
        ]);
    }
}

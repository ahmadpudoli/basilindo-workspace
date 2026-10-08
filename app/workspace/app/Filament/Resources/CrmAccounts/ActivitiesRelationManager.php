<?php

namespace App\Filament\Resources\CrmAccounts;

use App\Filament\Resources\CrmActivityResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Activities';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('subject')->columns([
            TextColumn::make('subject')->label('Subjek')->searchable()->sortable(),
            TextColumn::make('type')->label('Jenis')->badge(),
            TextColumn::make('due_at')->label('Jatuh tempo')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('completed_at')->label('Status')->formatStateUsing(fn ($state): string => $state ? 'Selesai' : 'Terbuka')->badge(),
        ])->headerActions([
            Action::make('createActivity')->label('Activity baru')->icon('heroicon-o-plus')->url(fn (): string => CrmActivityResource::getUrl('create', ['account_id' => $this->getOwnerRecord()->getKey()])),
        ])->recordActions([
            Action::make('editActivity')->label('Edit')->icon('heroicon-o-pencil-square')->url(fn ($record): string => CrmActivityResource::getUrl('edit', ['record' => $record])),
        ]);
    }
}

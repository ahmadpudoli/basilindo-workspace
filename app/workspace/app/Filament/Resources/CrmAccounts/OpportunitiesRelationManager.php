<?php

namespace App\Filament\Resources\CrmAccounts;

use App\Filament\Resources\CrmOpportunityResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OpportunitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'opportunities';
    protected static ?string $title = 'Opportunities';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('name')->columns([
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('stage')->label('Tahap')->badge(),
            TextColumn::make('amount')->label('Nilai')->numeric(decimalPlaces: 2),
            TextColumn::make('expected_close_date')->label('Target closing')->date(),
            TextColumn::make('project.name')->label('Project')->placeholder('Belum ada'),
        ])->headerActions([
            Action::make('createOpportunity')->label('Opportunity baru')->icon('heroicon-o-plus')->url(fn (): string => CrmOpportunityResource::getUrl('create', ['account_id' => $this->getOwnerRecord()->getKey()])),
        ])->recordActions([
            Action::make('editOpportunity')->label('Edit')->icon('heroicon-o-pencil-square')->url(fn ($record): string => CrmOpportunityResource::getUrl('edit', ['record' => $record])),
        ]);
    }
}

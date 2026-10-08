<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\TicketResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Tickets / tasks';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('uuid')->label('ID')->copyable(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('status.name')->label('Status')->badge(),
                TextColumn::make('priority.name')->label('Priority')->badge(),
                TextColumn::make('assignees.name')->label('Assignee')->badge()->separator(','),
                TextColumn::make('due_date')->label('Batas waktu')->date('d/m/Y')->sortable(),
            ])
            ->headerActions([
                Action::make('createTicket')
                    ->label('Ticket baru')
                    ->icon('heroicon-o-plus')
                    ->url(fn (): string => TicketResource::getUrl('create', ['project_id' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                Action::make('editTicket')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record): string => TicketResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\TicketComment;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Komentar';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('comment')->label('Komentar')->required()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label('Oleh'),
            TextColumn::make('comment')->label('Komentar')->limit(100),
            TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
        ])->headerActions([
            CreateAction::make()->mutateFormDataUsing(fn (array $data): array => [...$data, 'user_id' => auth()->id()]),
        ])->recordActions([
            EditAction::make()->visible(fn (TicketComment $record): bool => (int) $record->user_id === (int) auth()->id() || auth()->user()->hasRole('super_admin')),
            DeleteAction::make()->visible(fn (TicketComment $record): bool => (int) $record->user_id === (int) auth()->id() || auth()->user()->hasRole('super_admin')),
        ]);
    }
}

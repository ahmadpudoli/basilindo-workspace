<?php

namespace App\Filament\Resources;

use App\Filament\Support\HasWorkspaceModuleNavigation;

use Core\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    use HasWorkspaceModuleNavigation;
    protected static ?string $model = Ticket::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Tickets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Project')->options(fn (): array => static::projectOptions())->default(fn (): mixed => request()->query('project_id'))->searchable()->preload()->required()->live(),
            Select::make('ticket_status_id')->label('Status')->options(fn (Get $get): array => TicketStatus::query()->where(function (Builder $query) use ($get): void {
                $query->whereNull('project_id')->orWhere('project_id', $get('project_id'));
            })->orderBy('sort_order')->pluck('name', 'id')->all())->required()->searchable()->preload(),
            Select::make('priority_id')->label('Priority')->options(fn (): array => TicketPriority::query()->orderBy('sort_order')->pluck('name', 'id')->all())->searchable()->preload(),
            TextInput::make('name')->label('Nama ticket')->required()->maxLength(255),
            RichEditor::make('description')->label('Deskripsi')->columnSpanFull(),
            Select::make('assignees')->label('Ditugaskan kepada')->multiple()->relationship(
                'assignees',
                'name',
                modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->whereHas('projects', fn (Builder $projects): Builder => $projects->whereKey($get('project_id'))),
            )->searchable()->preload()->live(),
            DatePicker::make('start_date')->label('Tanggal mulai')->default(now()),
            DatePicker::make('due_date')->label('Batas waktu')->afterOrEqual('start_date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('uuid')->label('ID')->searchable()->copyable(),
            TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('status.name')->label('Status')->badge()->sortable(),
            TextColumn::make('priority.name')->label('Priority')->badge(),
            TextColumn::make('assignees.name')->label('Assignee')->badge()->separator(','),
            TextColumn::make('due_date')->label('Batas waktu')->date('d/m/Y')->sortable(),
        ])->filters([
            SelectFilter::make('project_id')->label('Project')->options(fn (): array => static::projectOptions()),
            SelectFilter::make('ticket_status_id')->label('Status')->options(fn (): array => TicketStatus::query()->whereNull('project_id')->pluck('name', 'id')->all()),
            SelectFilter::make('priority_id')->label('Priority')->options(fn (): array => TicketPriority::query()->pluck('name', 'id')->all()),
        ])->recordActions([ViewAction::make(), EditAction::make()])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['project', 'status', 'priority', 'assignees']);
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) {
            $query->whereHas('project.members', fn (Builder $members): Builder => $members->whereKey($user->getKey()));
        }
        return $query;
    }

    public static function getPages(): array
    {
        return ['index' => ListTickets::route('/'), 'create' => CreateTicket::route('/create'), 'view' => ViewTicket::route('/{record}'), 'edit' => EditTicket::route('/{record}/edit')];
    }

    private static function projectOptions(): array
    {
        $query = Project::query()->orderBy('name');
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) $query->whereHas('members', fn (Builder $members): Builder => $members->whereKey($user->getKey()));
        return $query->pluck('name', 'id')->all();
    }
}

<?php

namespace App\Filament\Resources\TicketStatuses;

use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Filament\Resources\TicketStatuses\Pages\CreateTicketStatus;
use App\Filament\Resources\TicketStatuses\Pages\EditTicketStatus;
use App\Filament\Resources\TicketStatuses\Pages\ListTicketStatuses;
use App\Models\TicketStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketStatusResource extends Resource
{
    use HasWorkspaceModuleNavigation;
    protected static ?string $model = TicketStatus::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationLabel = 'Ticket Statuses';
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('name')->required(), ColorPicker::make('color')->required(), TextInput::make('sort_order')->numeric()->default(0)]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('color'), TextColumn::make('sort_order')->sortable()])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]); }
    public static function getPages(): array { return ['index' => ListTicketStatuses::route('/'), 'create' => CreateTicketStatus::route('/create'), 'edit' => EditTicketStatus::route('/{record}/edit')]; }
}

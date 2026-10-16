<?php

namespace App\Filament\Resources\TicketPriorities;

use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Filament\Resources\TicketPriorities\Pages\CreateTicketPriority;
use App\Filament\Resources\TicketPriorities\Pages\EditTicketPriority;
use App\Filament\Resources\TicketPriorities\Pages\ListTicketPriorities;
use App\Models\TicketPriority;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketPriorityResource extends Resource
{
    use HasWorkspaceModuleNavigation;
    protected static ?string $model = TicketPriority::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationLabel = 'Ticket Priorities';
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('name')->required(), ColorPicker::make('color')->required(), TextInput::make('sort_order')->numeric()->default(0)]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('color'), TextColumn::make('sort_order')->sortable()])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]); }
    public static function getPages(): array { return ['index' => ListTicketPriorities::route('/'), 'create' => CreateTicketPriority::route('/create'), 'edit' => EditTicketPriority::route('/{record}/edit')]; }
}

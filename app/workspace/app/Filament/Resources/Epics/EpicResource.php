<?php

namespace App\Filament\Resources\Epics;

use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Filament\Resources\Epics\Pages\CreateEpic;
use App\Filament\Resources\Epics\Pages\EditEpic;
use App\Filament\Resources\Epics\Pages\ListEpics;
use App\Models\Epic;
use Core\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EpicResource extends Resource
{
    use HasWorkspaceModuleNavigation;

    protected static ?string $model = Epic::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Epics';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Project')->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->required(),
            TextInput::make('name')->label('Nama epic')->required()->maxLength(255),
            Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            DatePicker::make('start_date')->label('Tanggal mulai'),
            DatePicker::make('end_date')->label('Tanggal selesai')->afterOrEqual('start_date'),
            TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Epic')->searchable()->sortable(),
            TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
            TextColumn::make('start_date')->label('Mulai')->date('d/m/Y')->sortable(),
            TextColumn::make('end_date')->label('Selesai')->date('d/m/Y')->sortable(),
        ])->recordActions([ViewAction::make(), EditAction::make()])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListEpics::route('/'), 'create' => CreateEpic::route('/create'), 'edit' => EditEpic::route('/{record}/edit')];
    }
}

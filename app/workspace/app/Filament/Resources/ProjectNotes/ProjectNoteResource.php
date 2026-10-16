<?php

namespace App\Filament\Resources\ProjectNotes;

use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Filament\Resources\ProjectNotes\Pages\CreateProjectNote;
use App\Filament\Resources\ProjectNotes\Pages\EditProjectNote;
use App\Filament\Resources\ProjectNotes\Pages\ListProjectNotes;
use App\Models\ProjectNote;
use Core\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectNoteResource extends Resource
{
    use HasWorkspaceModuleNavigation;

    protected static ?string $model = ProjectNote::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Project Notes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->label('Project')->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->required(),
            TextInput::make('title')->label('Judul')->required()->maxLength(255),
            DatePicker::make('note_date')->label('Tanggal')->default(now())->required(),
            RichEditor::make('content')->label('Catatan')->required()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Judul')->searchable()->sortable(),
            TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
            TextColumn::make('note_date')->label('Tanggal')->date('d/m/Y')->sortable(),
            TextColumn::make('creator.name')->label('Dibuat oleh'),
        ])->recordActions([EditAction::make()])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListProjectNotes::route('/'), 'create' => CreateProjectNote::route('/create'), 'edit' => EditProjectNote::route('/{record}/edit')];
    }
}

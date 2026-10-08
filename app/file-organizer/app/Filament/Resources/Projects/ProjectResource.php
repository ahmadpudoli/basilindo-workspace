<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\Projects\RelationManagers\MembersRelationManager;
use App\Filament\Resources\Projects\RelationManagers\TicketsRelationManager;
use App\Filament\Resources\Projects\RelationManagers\DocumentsRelationManager;
use Core\Models\Company;
use Core\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            Select::make('company_id')
                ->label('Perusahaan')
                ->options(fn (): array => static::companyOptions())
                ->searchable()
                ->preload()
                ->required(),
            RichEditor::make('description')->columnSpanFull(),
            ColorPicker::make('color')->label('Warna proyek')->nullable(),
            DatePicker::make('start_date')->label('Tanggal mulai')->native(false)->displayFormat('d/m/Y'),
            DatePicker::make('end_date')
                ->label('Tanggal selesai')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->afterOrEqual('start_date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')->label('')->width('40px')->default('#6B7280'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Perusahaan')->sortable(),
                TextColumn::make('start_date')->date('d/m/Y')->sortable(),
                TextColumn::make('end_date')->date('d/m/Y')->sortable(),
                TextColumn::make('members_count')->counts('members')->label('Anggota')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MembersRelationManager::class,
            TicketsRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (! auth()->user()?->hasRole('super_admin')) {
            $query->whereHas('members', fn (Builder $members) => $members->where('user_id', auth()->id()));
        }
        return $query;
    }

    private static function companyOptions(): array
    {
        $query = Company::query()->where('status', 'active')->orderBy('name');
        $user = auth()->user();

        if ($user && ! $user->hasRole('super_admin')) {
            $query->whereIn('id', $user->companies()->pluck('core_companies.id'));
        }

        return $query->pluck('name', 'id')->all();
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Support\HasWorkspaceModuleNavigation;

use App\Filament\Resources\SavedSearchResource\Pages\CreateSavedSearch;
use App\Filament\Resources\SavedSearchResource\Pages\EditSavedSearch;
use App\Filament\Resources\SavedSearchResource\Pages\ListSavedSearches;
use App\Models\SavedSearch;
use Core\Models\Company;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SavedSearchResource extends Resource
{
    use HasWorkspaceModuleNavigation;
    protected static ?string $model = SavedSearch::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bookmark-square';
    protected static string|\UnitEnum|null $navigationGroup = 'File Organizer';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Pencarian tersimpan';
    protected static ?string $pluralModelLabel = 'Pencarian tersimpan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama pencarian')->required()->maxLength(160),
            Select::make('company_id')->label('Perusahaan')->options(fn (): array => static::companyOptions())->searchable()->preload()->required(),
            KeyValue::make('filters')->label('Filter pencarian')->keyLabel('Filter')->valueLabel('Nilai')
                ->helperText('Contoh: q, status, project_id, document_type_id, year, from, until, tag.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Perusahaan')->searchable(),
                TextColumn::make('updated_at')->label('Diubah')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('user_id', auth()->id());
        if (! auth()->user()?->hasRole('super_admin')) {
            $query->whereIn('company_id', auth()->user()?->companies()->select('core_companies.id') ?? []);
        }

        return $query;
    }

    private static function companyOptions(): array
    {
        $query = Company::query()->where('status', 'active')->orderBy('name');
        if (! auth()->user()?->hasRole('super_admin')) {
            $query->whereIn('id', auth()->user()?->companies()->select('core_companies.id') ?? []);
        }

        return $query->pluck('name', 'id')->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSavedSearches::route('/'),
            'create' => CreateSavedSearch::route('/create'),
            'edit' => EditSavedSearch::route('/{record}/edit'),
        ];
    }
}

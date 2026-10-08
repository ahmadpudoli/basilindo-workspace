<?php

namespace App\Filament\Resources\Vendors;

use App\Filament\Resources\Vendors\Pages\CreateVendor;
use App\Filament\Resources\Vendors\Pages\EditVendor;
use App\Filament\Resources\Vendors\Pages\ListVendors;
use App\Models\Vendor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VendorResource extends Resource
{
    // Legacy compatibility: vendor is now a business role on Company.
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Vendor::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|\UnitEnum|null $navigationGroup = 'File Organizer';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Vendor';
    protected static ?string $pluralModelLabel = 'Vendor';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')->label('Perusahaan')->relationship('company', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->label('Nama vendor')->required()->maxLength(255),
            TextInput::make('code')->label('Kode')->maxLength(50),
            TextInput::make('tax_identifier')->label('NPWP/identitas pajak')->maxLength(100),
            Select::make('status')->options(['active' => 'Aktif', 'inactive' => 'Tidak aktif'])->default('active')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('company.name')->label('Perusahaan')->searchable(),
            TextColumn::make('code')->label('Kode')->searchable(),
            TextColumn::make('tax_identifier')->label('Identitas pajak')->toggleable(),
            TextColumn::make('status')->badge(),
        ])->filters([
            SelectFilter::make('company_id')->relationship('company', 'name')->searchable(),
            SelectFilter::make('status')->options(['active' => 'Aktif', 'inactive' => 'Tidak aktif']),
        ])->recordActions([EditAction::make()])
          ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (! auth()->user()?->hasRole('super_admin')) {
            $query->whereIn('company_id', auth()->user()?->companies()->select('core_companies.id') ?? []);
        }
        return $query;
    }

    public static function getPages(): array
    {
        return ['index' => ListVendors::route('/'), 'create' => CreateVendor::route('/create'), 'edit' => EditVendor::route('/{record}/edit')];
    }
}

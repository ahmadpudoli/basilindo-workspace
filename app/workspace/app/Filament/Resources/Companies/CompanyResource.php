<?php

namespace App\Filament\Resources\Companies;

use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Core\Models\Company;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyResource extends Resource
{
    use HasWorkspaceModuleNavigation;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(session('workspace.active_module'), ['core', 'documents'], true);
    }
    protected static ?string $model = Company::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Master Perusahaan';
    protected static ?string $modelLabel = 'Entitas bisnis';
    protected static ?string $pluralModelLabel = 'Master Perusahaan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama perusahaan / pihak')->required()->maxLength(255),
            TextInput::make('code')->label('Kode')->required()->alphaDash()->maxLength(50),
            Select::make('entity_type')->label('Jenis entitas')->options([
                'group' => 'Group',
                'company' => 'Perusahaan',
            ])->default('company')->required(),
            Select::make('classification')->label('Klasifikasi')->options([
                'internal' => 'Internal Group Basilindo',
                'external' => 'Eksternal',
            ])->default('external')->required(),
            CheckboxList::make('roles')->label('Peran bisnis')->options([
                'operating_company' => 'Perusahaan operasional',
                'client' => 'Client',
                'vendor' => 'Vendor',
                'partner' => 'Partner',
            ])->columns(2),
            Select::make('parent_company_id')->label('Induk perusahaan')->relationship('parent', 'name')->searchable()->preload(),
            Select::make('status')->label('Status')->options(['active' => 'Aktif', 'inactive' => 'Tidak aktif'])->default('active')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('classification')->label('Klasifikasi')->badge(),
                TextColumn::make('roles')
                    ->label('Peran bisnis')
                    ->formatStateUsing(fn ($state): string => collect($state)->map(fn (string $role): string => match ($role) {
                        'group' => 'Group',
                        'operating_company' => 'Perusahaan operasional',
                        'client' => 'Client',
                        'vendor' => 'Vendor',
                        'partner' => 'Partner',
                        default => $role,
                    })->join(', ') ?: '-')
                    ->badge(),
                TextColumn::make('parent.name')->label('Induk')->default('-')->searchable(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('projects_count')->counts('projects')->label('Proyek'),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('classification')->label('Klasifikasi')->options([
                    'internal' => 'Internal Group Basilindo',
                    'external' => 'Eksternal',
                ]),
                SelectFilter::make('business_role')
                    ->label('Peran bisnis')
                    ->options([
                        'operating_company' => 'Perusahaan operasional',
                        'client' => 'Client',
                        'vendor' => 'Vendor',
                        'partner' => 'Partner',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $role): Builder => $query->whereJsonContains('roles', $role),
                    )),
                SelectFilter::make('status')->options(['active' => 'Aktif', 'inactive' => 'Tidak aktif']),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCompanies::route('/'), 'create' => CreateCompany::route('/create'), 'edit' => EditCompany::route('/{record}/edit')];
    }
}

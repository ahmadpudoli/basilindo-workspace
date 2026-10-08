<?php

namespace App\Filament\Resources\CrmAccounts;

use App\Filament\Support\HasWorkspaceModuleNavigation;

use App\Filament\Resources\CrmAccounts\Pages\CreateCrmAccount;
use App\Filament\Resources\CrmAccounts\Pages\EditCrmAccount;
use App\Filament\Resources\CrmAccounts\Pages\ListCrmAccounts;
use App\Filament\Resources\CrmAccounts\Pages\ViewCrmAccount;
use App\Filament\Resources\CrmAccounts\ActivitiesRelationManager;
use App\Filament\Resources\CrmAccounts\ContactsRelationManager;
use App\Filament\Resources\CrmAccounts\DocumentsRelationManager;
use App\Filament\Resources\CrmAccounts\OpportunitiesRelationManager;
use Core\Models\Company;
use App\Models\CrmAccount;
use Core\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrmAccountResource extends Resource
{
    use HasWorkspaceModuleNavigation;
    protected static ?string $model = CrmAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Accounts';

    protected static ?string $modelLabel = 'Account';

    protected static ?string $pluralModelLabel = 'Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label('Perusahaan')
                ->options(fn (): array => static::companyOptions())
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('name')
                ->label('Nama account/customer')
                ->required()
                ->maxLength(255),
            Select::make('status')
                ->label('Status')
                ->options([
                    'prospect' => 'Prospek',
                    'customer' => 'Customer',
                    'inactive' => 'Tidak aktif',
                ])
                ->default('prospect')
                ->required(),
            TextInput::make('industry')->label('Industri')->maxLength(150),
            TextInput::make('segment')->label('Segmen')->maxLength(150),
            Select::make('owner_user_id')
                ->label('Owner')
                ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->preload(),
            TextInput::make('estimated_value')->label('Estimasi nilai')->numeric()->minValue(0),
            TextInput::make('currency')->label('Mata uang')->default('IDR')->length(3)->alpha()->maxLength(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Account/customer')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Perusahaan')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('industry')->label('Industri')->toggleable(),
                TextColumn::make('segment')->label('Segmen')->toggleable(),
                TextColumn::make('contacts_count')->label('Contacts')->counts('contacts')->sortable(),
                TextColumn::make('opportunities_count')->label('Opportunities')->counts('opportunities')->sortable(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options([
                    'prospect' => 'Prospek',
                    'customer' => 'Customer',
                    'inactive' => 'Tidak aktif',
                ]),
                SelectFilter::make('company_id')
                    ->label('Perusahaan')
                    ->options(fn (): array => static::companyOptions()),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['company']);
        $user = auth()->user();

        if ($user && ! $user->hasRole('super_admin')) {
            $companyIds = $user->companies()->pluck('core_companies.id');
            $query->whereIn('company_id', $companyIds);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmAccounts::route('/'),
            'create' => CreateCrmAccount::route('/create'),
            'view' => ViewCrmAccount::route('/{record}'),
            'edit' => EditCrmAccount::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
            OpportunitiesRelationManager::class,
            ActivitiesRelationManager::class,
            DocumentsRelationManager::class,
        ];
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

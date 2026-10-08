<?php

namespace App\Filament\Resources;

use Core\Models\Company;
use App\Models\CrmAccount;
use App\Models\CrmLead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrmLeadResource extends Resource
{
    protected static ?string $model = CrmLead::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Leads';
    protected static ?string $modelLabel = 'Lead';
    protected static ?string $pluralModelLabel = 'Leads';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')->label('Perusahaan')->options(fn (): array => static::companyOptions())->searchable()->preload(),
            Select::make('account_id')->label('Account terkait')->options(fn (): array => static::accountOptions())->searchable()->preload(),
            TextInput::make('name')->label('Nama lead')->required()->maxLength(255),
            TextInput::make('email')->label('Email')->email()->maxLength(255),
            TextInput::make('phone')->label('Nomor telepon')->tel()->maxLength(50),
            TextInput::make('source')->label('Sumber lead')->maxLength(100),
            Select::make('status')->label('Status')->options(['new' => 'Baru', 'contacted' => 'Sudah dihubungi', 'qualified' => 'Qualified', 'converted' => 'Converted', 'lost' => 'Lost'])->default('new')->required(),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('company.name')->label('Perusahaan')->searchable()->sortable(),
            TextColumn::make('account.name')->label('Account')->searchable(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('source')->label('Sumber'),
            TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('status')->label('Status')->options(['new' => 'Baru', 'contacted' => 'Sudah dihubungi', 'qualified' => 'Qualified', 'converted' => 'Converted', 'lost' => 'Lost']),
        ])->recordActions([EditAction::make()])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['company', 'account']);
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) $query->whereIn('company_id', $user->companies()->pluck('core_companies.id'));
        return $query;
    }

    public static function getPages(): array
    {
        return ['index' => ListCrmLeads::route('/'), 'create' => CreateCrmLead::route('/create'), 'edit' => EditCrmLead::route('/{record}/edit')];
    }

    private static function companyOptions(): array
    {
        $query = Company::query()->where('status', 'active')->orderBy('name');
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) $query->whereIn('id', $user->companies()->pluck('core_companies.id'));
        return $query->pluck('name', 'id')->all();
    }

    private static function accountOptions(): array
    {
        $query = CrmAccount::query()->orderBy('name');
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) $query->whereIn('company_id', $user->companies()->pluck('core_companies.id'));
        return $query->pluck('name', 'id')->all();
    }
}

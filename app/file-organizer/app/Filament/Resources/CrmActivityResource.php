<?php

namespace App\Filament\Resources;

use Core\Models\Company;
use App\Models\CrmAccount;
use App\Models\CrmActivity;
use App\Models\CrmOpportunity;
use Core\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrmActivityResource extends Resource
{
    protected static ?string $model = CrmActivity::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Activities';
    protected static ?string $modelLabel = 'Activity';
    protected static ?string $pluralModelLabel = 'Activities';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')->label('Perusahaan')->options(fn (): array => static::companyOptions())->searchable()->preload()->required(),
            Select::make('account_id')->label('Account')->options(fn (): array => static::accountOptions())->default(fn (): mixed => request()->query('account_id'))->searchable()->preload(),
            Select::make('opportunity_id')->label('Opportunity')->options(fn (): array => static::opportunityOptions())->searchable()->preload(),
            Select::make('assigned_to')->label('Ditugaskan kepada')->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload(),
            Select::make('type')->label('Jenis')->options(['call' => 'Call', 'meeting' => 'Meeting', 'email' => 'Email', 'task' => 'Task', 'note' => 'Note'])->default('note')->required(),
            TextInput::make('subject')->label('Subjek')->required()->maxLength(255),
            Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            DateTimePicker::make('due_at')->label('Jatuh tempo'),
            DateTimePicker::make('completed_at')->label('Selesai pada'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subject')->label('Subjek')->searchable()->sortable(),
            TextColumn::make('type')->label('Jenis')->badge(),
            TextColumn::make('company.name')->label('Perusahaan')->searchable(),
            TextColumn::make('account.name')->label('Account')->searchable(),
            TextColumn::make('due_at')->label('Jatuh tempo')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('completed_at')->label('Status')->formatStateUsing(fn ($state): string => $state ? 'Selesai' : 'Terbuka')->badge(),
        ])->filters([
            SelectFilter::make('type')->label('Jenis')->options(['call' => 'Call', 'meeting' => 'Meeting', 'email' => 'Email', 'task' => 'Task', 'note' => 'Note']),
        ])->recordActions([EditAction::make()])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ])->defaultSort('due_at', 'asc');
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
        return ['index' => ListCrmActivities::route('/'), 'create' => CreateCrmActivity::route('/create'), 'edit' => EditCrmActivity::route('/{record}/edit')];
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

    private static function opportunityOptions(): array
    {
        $query = CrmOpportunity::query()->with('account')->orderBy('name');
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) {
            $query->whereHas('account', fn ($accountQuery) => $accountQuery->whereIn('company_id', $user->companies()->pluck('core_companies.id')));
        }
        return $query->pluck('name', 'id')->all();
    }
}

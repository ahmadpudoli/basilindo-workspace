<?php

namespace App\Filament\Resources;

use App\Models\CrmAccount;
use App\Models\CrmOpportunity;
use App\Services\ConvertOpportunityToProject;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrmOpportunityResource extends Resource
{
    protected static ?string $model = CrmOpportunity::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Opportunities';
    protected static ?string $modelLabel = 'Opportunity';
    protected static ?string $pluralModelLabel = 'Opportunities';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('account_id')->label('Account/customer')->options(fn (): array => static::accountOptions())->default(fn (): mixed => request()->query('account_id'))->searchable()->preload()->required(),
            TextInput::make('name')->label('Nama opportunity')->required()->maxLength(255),
            Select::make('stage')->label('Tahap pipeline')->options(['qualification' => 'Qualification', 'proposal' => 'Proposal', 'negotiation' => 'Negotiation', 'won' => 'Won', 'lost' => 'Lost'])->default('qualification')->required(),
            TextInput::make('amount')->label('Nilai')->numeric()->minValue(0),
            TextInput::make('currency')->label('Mata uang')->default('IDR')->length(3)->alpha()->maxLength(3),
            DatePicker::make('expected_close_date')->label('Target closing'),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Opportunity')->searchable()->sortable(),
            TextColumn::make('account.name')->label('Account/customer')->searchable()->sortable(),
            TextColumn::make('stage')->label('Tahap')->badge(),
            TextColumn::make('amount')->label('Nilai')->numeric(decimalPlaces: 2),
            TextColumn::make('expected_close_date')->label('Target closing')->date(),
            TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('stage')->label('Tahap')->options(['qualification' => 'Qualification', 'proposal' => 'Proposal', 'negotiation' => 'Negotiation', 'won' => 'Won', 'lost' => 'Lost']),
        ])->recordActions([
            EditAction::make(),
            Action::make('convertToProject')
                ->label('Buat project')
                ->icon('heroicon-o-rectangle-stack')
                ->color('success')
                ->visible(fn (CrmOpportunity $record): bool => auth()->user()?->can('convertToProject', $record) ?? false)
                ->form([
                    TextInput::make('project_name')->label('Nama project')->default(fn (CrmOpportunity $record): string => $record->name)->required()->maxLength(255),
                    Textarea::make('description')->label('Deskripsi'),
                    DatePicker::make('start_date')->label('Tanggal mulai')->default(now()->toDateString()),
                    DatePicker::make('end_date')->label('Tanggal selesai')->afterOrEqual('start_date'),
                ])
                ->action(function (CrmOpportunity $record, array $data): void {
                    app(ConvertOpportunityToProject::class)->handle(
                        $record,
                        $data['project_name'],
                        $data['description'] ?? null,
                        $data['start_date'] ?? null,
                        $data['end_date'] ?? null,
                    );
                })
                ->successNotificationTitle('Project berhasil dibuat dari opportunity'),
        ])->toolbarActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('account');
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) {
            $query->whereHas('account', fn (Builder $accountQuery): Builder => $accountQuery->whereIn('company_id', $user->companies()->pluck('core_companies.id')));
        }
        return $query;
    }

    public static function getPages(): array
    {
        return ['index' => ListCrmOpportunities::route('/'), 'create' => CreateCrmOpportunity::route('/create'), 'edit' => EditCrmOpportunity::route('/{record}/edit')];
    }

    private static function accountOptions(): array
    {
        $query = CrmAccount::query()->orderBy('name');
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin')) $query->whereIn('company_id', $user->companies()->pluck('core_companies.id'));
        return $query->pluck('name', 'id')->all();
    }
}

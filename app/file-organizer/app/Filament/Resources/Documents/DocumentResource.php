<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Pages\ViewDocument;
use App\Models\Document;
use App\Models\CrmAccount;
use App\Models\CrmOpportunity;
use Core\Models\Company;
use Core\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Documents\DocumentQueryService;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static string|\UnitEnum|null $navigationGroup = 'File Organizer';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Dokumen';
    protected static ?string $pluralModelLabel = 'Dokumen';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('upload')->label('File dokumen')->required(fn (string $operation): bool => $operation === 'create')->storeFiles(false)->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(51200),
            TextInput::make('title')->label('Judul')->required()->maxLength(255),
            Select::make('company_id')->label('Perusahaan')->options(fn (): array => static::companyOptions())->searchable()->preload()->required()->live(),
            Select::make('project_id')
                ->label('Proyek')
                ->options(fn (Get $get): array => static::projectOptions($get->get('company_id')))
                ->default(fn (): mixed => request()->query('project_id'))
                ->searchable()
                ->preload(),
            Select::make('crm_account_id')
                ->label('CRM Account')
                ->options(fn (Get $get): array => CrmAccount::query()
                    ->when(! auth()->user()?->hasRole('super_admin'), fn ($query) => $query->whereIn('company_id', static::visibleCompanyIds()))
                    ->when($get('company_id'), fn ($query, $companyId) => $query->where('company_id', $companyId))
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->default(fn (): mixed => request()->query('crm_account_id'))
                ->searchable()
                ->preload()
                ->live(),
            Select::make('crm_opportunity_id')
                ->label('CRM Opportunity')
                ->options(fn (Get $get): array => CrmOpportunity::query()
                    ->whereHas('account', fn ($query) => $query->when(! auth()->user()?->hasRole('super_admin'), fn ($companyQuery) => $companyQuery->whereIn('company_id', static::visibleCompanyIds())))
                    ->when($get('crm_account_id'), fn ($query, $accountId) => $query->where('account_id', $accountId))
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload(),
            Select::make('vendor_id')->label('Vendor')->relationship('vendor', 'name')->searchable()->preload(),
            Select::make('document_type_id')->label('Jenis dokumen')->relationship('documentType', 'name')->searchable()->preload()->required(),
            TextInput::make('reference_number')->label('Nomor referensi')->maxLength(255),
            TagsInput::make('tags')->label('Tag')->separator(','),
            KeyValue::make('metadata')->label('Metadata tambahan')->keyLabel('Nama field')->valueLabel('Nilai'),
            DatePicker::make('document_date')->label('Tanggal dokumen')->native(false),
            TextInput::make('amount')->label('Nominal')->numeric()->minValue(0),
            TextInput::make('currency')->label('Mata uang')->default('IDR')->length(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Perusahaan')->searchable(),
                TextColumn::make('project.name')->label('Proyek')->searchable(),
                TextColumn::make('crmAccount.name')->label('CRM Account')->searchable()->toggleable(),
                TextColumn::make('crmOpportunity.name')->label('CRM Opportunity')->searchable()->toggleable(),
                TextColumn::make('vendor.name')->label('Vendor')->searchable(),
                TextColumn::make('documentType.name')->label('Jenis')->badge(),
                TextColumn::make('reference_number')->label('Nomor referensi')->searchable(),
                TextColumn::make('document_date')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Perusahaan')->relationship('company', 'name')->searchable(),
                SelectFilter::make('document_type_id')->label('Jenis dokumen')->relationship('documentType', 'name'),
                SelectFilter::make('status')->options(['quarantine' => 'Quarantine', 'pending' => 'Menunggu', 'ready' => 'Siap', 'rejected' => 'Ditolak']),
                Filter::make('document_date')->form([DatePicker::make('from')->label('Dari'), DatePicker::make('until')->label('Sampai')])->query(fn (Builder $query, array $data) => $query->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('document_date', '>=', $date))->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('document_date', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('release')
                    ->label('Rilis')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Document $record): bool => $record->status === 'quarantine' && (auth()->user()?->can('review', $record) ?? false))
                    ->requiresConfirmation()
                    ->action(function (Document $record): void {
                        abort_unless(auth()->user()?->can('review', $record), 403);
                        app(\App\Services\Documents\DocumentLifecycleService::class)->release($record, (int) auth()->id());
                    })
                    ->successNotificationTitle('Dokumen dirilis'),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Document $record): bool => $record->status === 'quarantine' && (auth()->user()?->can('review', $record) ?? false))
                    ->form([Textarea::make('reason')->label('Alasan penolakan')->required()->maxLength(2000)])
                    ->action(function (Document $record, array $data): void {
                        abort_unless(auth()->user()?->can('review', $record), 403);
                        app(\App\Services\Documents\DocumentLifecycleService::class)->reject($record, (int) auth()->id(), $data['reason']);
                    })
                    ->successNotificationTitle('Dokumen ditolak'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDocuments::route('/'), 'create' => CreateDocument::route('/create'), 'view' => ViewDocument::route('/{record}'), 'edit' => EditDocument::route('/{record}/edit')];
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DocumentQueryService::class)->visibleTo(auth()->user());
    }

    private static function visibleCompanyIds(): array
    {
        return auth()->user()?->companies()->pluck('core_companies.id')->all() ?? [];
    }

    private static function companyOptions(): array
    {
        $query = Company::query()->where('status', 'active')->orderBy('name');

        if (! auth()->user()?->hasRole('super_admin')) {
            $query->whereIn('id', static::visibleCompanyIds());
        }

        return $query->pluck('name', 'id')->all();
    }

    private static function projectOptions(mixed $companyId = null): array
    {
        $query = Project::query()->orderBy('name');
        $user = auth()->user();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($user && ! $user->hasRole('super_admin')) {
            $query->whereHas('members', fn (Builder $members) => $members->whereKey($user->getKey()));
        }

        return $query->pluck('name', 'id')->all();
    }
}

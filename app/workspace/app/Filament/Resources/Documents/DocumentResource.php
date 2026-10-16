<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Pages\ViewDocument;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Models\Document;
use App\Models\DocumentType;
use App\Services\Documents\DocumentQueryService;
use App\Services\Documents\DocumentCompanyScope;
use App\Services\Documents\DocumentMetadataValueNormalizer;
use Core\Models\Company;
use Core\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DocumentResource extends Resource
{
    use HasWorkspaceModuleNavigation;

    protected static ?string $model = Document::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string|\UnitEnum|null $navigationGroup = 'Document Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->extraAttributes(['novalidate' => true])
            ->components([
            Section::make('File dan informasi utama')
                ->schema([
                    FileUpload::make('upload')->label('File dokumen')->required(fn (string $operation): bool => $operation === 'create')->storeFiles(false)->panelLayout('compact')->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(51200),
                    TextInput::make('title')->label('Judul')->required()->maxLength(255),
                    RichEditor::make('description')
                        ->label('Deskripsi Dokumen')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->compact(),
            Section::make('Klasifikasi dan relasi')
                ->schema([
                    Select::make('company_id')
                        ->label('Perusahaan internal')
                        ->options(fn (): array => static::internalCompanyOptions())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set): void {
                            $set('project_id', null);
                            $set('document_type_id', null);
                            $set('metadata', []);
                        }),
                    Select::make('counterparty_company_id')
                        ->label('Perusahaan client/vendor')
                        ->options(fn (): array => static::counterpartyCompanyOptions())
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('project_id')
                        ->label('Proyek')
                        ->options(fn (Get $get): array => static::projectOptions($get('company_id')))
                        ->default(fn (): mixed => request()->query('project_id'))
                        ->placeholder(fn (Get $get): string => filled($get('company_id')) ? 'Pilih proyek (opsional)' : 'Pilih perusahaan internal terlebih dahulu')
                        ->disabled(fn (Get $get): bool => blank($get('company_id')))
                        ->searchable()
                        ->preload(),
                    Select::make('document_type_id')
                        ->label('Jenis dokumen')
                        ->options(fn (Get $get): array => static::documentTypeOptions($get('company_id')))
                        ->placeholder(fn (Get $get): string => filled($get('company_id')) ? 'Pilih jenis dokumen' : 'Pilih perusahaan internal terlebih dahulu')
                        ->disabled(fn (Get $get): bool => blank($get('company_id')))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set): mixed => $set('metadata', [])),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->compact(),
            Section::make('Metadata dokumen')
                ->key('documentMetadataPanel')
                ->statePath('metadata')
                ->description('Field berikut mengikuti konfigurasi Jenis Dokumen yang dipilih.')
                ->schema(fn (Get $get): array => static::metadataSchema($get('document_type_id')))
                ->columns(3)
                ->columnSpanFull()
                ->compact()
                ->visible(fn (Get $get): bool => filled($get('document_type_id'))),
            Section::make('Memuat metadata tambahan')
                ->extraAttributes([
                    'wire:loading.flex' => true,
                    'wire:target' => 'data.company_id,data.document_type_id',
                    'class' => 'rounded-lg bg-blue-50 text-blue-700',
                ])
                ->schema([
                    Placeholder::make('metadata_loading_message')
                        ->hiddenLabel()
                        ->content('Memuat konfigurasi metadata sesuai perusahaan dan jenis dokumen...')
                        ->extraAttributes(['class' => 'px-4 py-3 text-sm']),
                ])
                ->columnSpanFull()
                ->compact(),
            Section::make('Detail dokumen')
                ->schema([
                    DatePicker::make('document_date')->label('Tanggal dokumen')->native(false)->required(),
                    TextInput::make('reference_number')->label('Nomor referensi')->maxLength(255),
                    TagsInput::make('tags')->label('Tag')->separator(','),
                    TextInput::make('amount')
                        ->label('Nominal')
                        ->type('text')
                        ->inputMode('decimal')
                        ->minValue(0)
                        ->formatStateUsing(fn (mixed $state): mixed => DocumentMetadataValueNormalizer::formatIndonesian($state))
                        ->extraInputAttributes([
                            'inputmode' => 'decimal',
                            'oninput' => "let value = this.value.replace(/[^0-9.,-]/g, '').replace(/(?!^)-/g, ''); let sign = value.startsWith('-') ? '-' : ''; value = value.replace(/-/g, ''); let hasDecimal = value.includes(','); let parts = hasDecimal ? value.split(',') : [value]; let integer = parts.shift().replace(/\\./g, '').replace(/^0+(?=\\d)/, ''); let fraction = parts.join('').replace(/\\D/g, ''); this.value = sign + integer.replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.') + (hasDecimal ? ',' + fraction : '')",
                        ])
                        ->validationMessages([
                            'numeric' => 'Nominal harus berupa angka desimal yang valid.',
                            'min' => 'Nominal tidak boleh kurang dari :min.',
                        ])
                        ->dehydrateStateUsing(fn (mixed $state): mixed => DocumentMetadataValueNormalizer::numeric($state, 'numeric_decimal')),
                    Select::make('currency')
                        ->label('Mata uang')
                        ->options(['IDR' => 'IDR', 'USD' => 'USD', 'SGD' => 'SGD'])
                        ->placeholder('Pilih mata uang (opsional)')
                        ->searchable(),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->compact(),
        ]);
    }

    /** @return array<int, \Filament\Forms\Components\Field> */
    protected static function metadataFields(mixed $documentTypeId): array
    {
        if (! $documentTypeId) {
            return [];
        }

        $fields = DocumentType::query()->find((int) $documentTypeId)?->metadataDefinitions() ?? [];

        return collect($fields)
            ->map(fn (array $config, string $name): array => ['name' => $name, ...$config])
            ->filter(fn (array $config): bool => preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $config['name']) === 1)
            ->map(function (array $config) {
                $name = $config['name'];
                $type = $config['type'];
                $field = match ($type) {
                    'numeric' => TextInput::make($name)
                        ->type('text')
                        ->inputMode('numeric')
                        ->step(1)
                        ->regex('/^-?(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+)$/')
                        ->extraInputAttributes([
                            'inputmode' => 'numeric',
                            'oninput' => "let value = this.value.replace(/[^0-9-]/g, '').replace(/(?!^)-/g, ''); let sign = value.startsWith('-') ? '-' : ''; let digits = value.replace(/-/g, '').replace(/^0+(?=\\d)/, ''); this.value = sign + digits.replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.')",
                        ])
                        ->validationMessages([
                            'numeric' => 'Field ini harus berisi angka bulat tanpa huruf atau desimal.',
                            'regex' => 'Field ini harus berisi angka bulat tanpa desimal.',
                        ])
                        ->dehydrateStateUsing(fn (mixed $state): mixed => DocumentMetadataValueNormalizer::numeric($state, 'numeric')),
                    'numeric_decimal' => TextInput::make($name)
                        ->type('text')
                        ->inputMode('decimal')
                        ->step('any')
                        ->regex('/^-?(?:[0-9]+(?:\.[0-9]+)?|[0-9]{1,3}(?:\.[0-9]{3})+(?:,[0-9]+)?)$/')
                        ->extraInputAttributes([
                            'inputmode' => 'decimal',
                            'oninput' => "let value = this.value.replace(/[^0-9.,-]/g, '').replace(/(?!^)-/g, ''); let sign = value.startsWith('-') ? '-' : ''; value = value.replace(/-/g, ''); let hasDecimal = value.includes(','); let parts = hasDecimal ? value.split(',') : [value]; let integer = parts.shift().replace(/\\./g, '').replace(/^0+(?=\\d)/, ''); let fraction = parts.join('').replace(/\\D/g, ''); this.value = sign + integer.replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.') + (hasDecimal ? ',' + fraction : '')",
                        ])
                        ->validationMessages([
                            'numeric' => 'Field ini harus berisi angka bulat atau desimal yang valid.',
                            'regex' => 'Field ini harus berisi angka desimal yang valid, misalnya 125000,75.',
                        ])
                        ->dehydrateStateUsing(fn (mixed $state): mixed => DocumentMetadataValueNormalizer::numeric($state, 'numeric_decimal')),
                    'date' => DatePicker::make($name)->native(false),
                    'datetime' => DateTimePicker::make($name)->native(false),
                    default => TextInput::make($name),
                };

                return $field
                    ->label(Str::headline($name))
                    ->validationAttribute(Str::headline($name))
                    ->required($config['mandatory'])
                    ->dehydrated(true);
            })
            ->values()
            ->all();
    }

    /** @return array<int, \Filament\Schemas\Components\Component> */
    protected static function metadataSchema(mixed $documentTypeId): array
    {
        $fields = static::metadataFields($documentTypeId);

        if (filled($documentTypeId) && $fields === []) {
            $fields[] = Placeholder::make('metadata_empty')
                ->content('Belum ada metadata valid yang dikonfigurasi untuk jenis dokumen ini.')
                ->columnSpanFull();
        }

        return $fields;
    }

    /** @return array<int|string, string> */
    protected static function documentTypeOptions(mixed $companyId = null): array
    {
        if (blank($companyId)) {
            return [];
        }

        return DocumentType::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($companyId): void {
                $query->whereNull('company_id');

                if (filled($companyId)) {
                    $query->orWhere('company_id', $companyId);
                }
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Perusahaan internal')->searchable(),
                TextColumn::make('counterpartyCompany.name')->label('Client/vendor')->searchable(),
                TextColumn::make('project.name')->label('Proyek')->searchable(),
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

    private static function internalCompanyOptions(): array
    {
        $query = Company::query()->where('status', 'active')->where('classification', 'internal')->orderBy('name');
        $companyIds = app(DocumentCompanyScope::class)->ids(auth()->user());

        if ($companyIds !== null) {
            $query->whereIn('id', $companyIds);
        }

        return $query->pluck('name', 'id')->all();
    }

    private static function counterpartyCompanyOptions(): array
    {
        return Company::query()
            ->where('status', 'active')
            ->where('classification', 'external')
            ->where(function (Builder $query): void {
                $query->whereJsonContains('roles', 'client')
                    ->orWhereJsonContains('roles', 'vendor');
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    private static function projectOptions(mixed $companyId = null): array
    {
        if (blank($companyId)) {
            return [];
        }

        $query = Project::query()->orderBy('name');
        $user = auth()->user();

        $query->where('company_id', $companyId);

        if ($user && ! $user->hasRole('super_admin')) {
            $query->whereHas('members', fn (Builder $members) => $members->whereKey($user->getKey()));
        }

        return $query->pluck('name', 'id')->all();
    }
}

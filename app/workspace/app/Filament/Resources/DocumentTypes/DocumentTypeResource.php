<?php

namespace App\Filament\Resources\DocumentTypes;

use App\Filament\Resources\DocumentTypes\Pages\CreateDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Models\DocumentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentTypeResource extends Resource
{
    use HasWorkspaceModuleNavigation;

    protected static ?string $model = DocumentType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Document Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Jenis dokumen';

    protected static ?string $pluralModelLabel = 'Jenis dokumen';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label('Perusahaan')
                ->relationship('company', 'name')
                ->searchable()
                ->preload()
                ->helperText('Kosongkan untuk menjadikan jenis dokumen berlaku global. Jika sudah digunakan dokumen, perusahaan tidak dapat diubah; buat jenis dokumen baru.'),
            TextInput::make('name')->label('Nama')->required()->maxLength(255),
            TextInput::make('code')->label('Kode')->required()->alphaDash()->maxLength(50),
            Repeater::make('metadata_rows')
                ->label('Metadata tambahan')
                ->schema([
                    TextInput::make('field_name')
                        ->label('Nama field')
                        ->required()
                        ->regex('/^[A-Za-z][A-Za-z0-9_]*$/')
                        ->distinct(),
                    Select::make('type')
                        ->label('Tipe')
                        ->options([
                            'string' => 'String',
                            'numeric' => 'Numeric',
                            'numeric_decimal' => 'Numeric Decimal',
                            'date' => 'Date',
                            'datetime' => 'Datetime',
                        ])
                        ->required(),
                    Select::make('mandatory')
                        ->label('Mandatory')
                        ->options(['1' => 'Ya', '0' => 'Tidak'])
                        ->default('1')
                        ->required(),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->helperText('Tipe date dan datetime menjadi datepicker. Jika sudah digunakan dokumen, metadata tidak dapat diubah atau dihapus; buat jenis dokumen baru.'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    /** @return array<int, array{name: string, type: string, mandatory: string}> */
    public static function metadataRows(mixed $state): array
    {
        if (! is_array($state)) {
            return [];
        }

        $isList = array_is_list($state);

        return collect($state)->map(function ($config, $name) use ($isList): array {
            if (is_array($config)) {
                return [
                    'field_name' => (string) ($config['field_name'] ?? $config['name'] ?? ($isList ? '' : $name)),
                    'type' => strtolower((string) ($config['type'] ?? 'string')),
                    'mandatory' => filter_var($config['mandatory'] ?? true, FILTER_VALIDATE_BOOLEAN) ? '1' : '0',
                ];
            }

            return [
                'field_name' => (string) $name,
                'type' => strtolower((string) $config),
                'mandatory' => '1',
            ];
        })->values()->all();
    }

    /** @return array<string, array{type: string, mandatory: bool}> */
    public static function metadataMap(mixed $state): array
    {
        if (! is_array($state)) {
            return [];
        }

        return collect($state)
            ->filter(fn ($row): bool => is_array($row) && filled($row['field_name'] ?? $row['name'] ?? null))
            ->mapWithKeys(function (array $row): array {
                $name = trim((string) ($row['field_name'] ?? $row['name']));

                return [$name => [
                    'type' => strtolower((string) ($row['type'] ?? 'string')),
                    'mandatory' => (string) ($row['mandatory'] ?? '1') === '1',
                ]];
            })
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('code')->label('Kode')->searchable(),
            TextColumn::make('company.name')->label('Perusahaan')->default('Global')->searchable(),
            TextColumn::make('documents_count')->counts('documents')->label('Dokumen'),
            TextColumn::make('is_active')->label('Status')->badge()->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif'),
        ])->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (! auth()->user()?->hasRole('super_admin')) {
            $query->where(function (Builder $q) {
                $q->whereNull('company_id')->orWhereIn('company_id', auth()->user()?->companies()->select('core_companies.id') ?? []);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return ['index' => ListDocumentTypes::route('/'), 'create' => CreateDocumentType::route('/create'), 'edit' => EditDocumentType::route('/{record}/edit')];
    }
}

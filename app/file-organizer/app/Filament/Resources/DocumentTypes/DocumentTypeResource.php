<?php

namespace App\Filament\Resources\DocumentTypes;

use App\Filament\Resources\DocumentTypes\Pages\CreateDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Models\DocumentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
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
    protected static ?string $model = DocumentType::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'File Organizer';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Jenis dokumen';
    protected static ?string $pluralModelLabel = 'Jenis dokumen';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')->label('Perusahaan')->relationship('company', 'name')->searchable()->preload(),
            TextInput::make('name')->label('Nama')->required()->maxLength(255),
            TextInput::make('code')->label('Kode')->required()->alphaDash()->maxLength(50),
            KeyValue::make('required_fields')->label('Metadata wajib')->keyLabel('Nama field')->valueLabel('Tipe')->helperText('Contoh: nomor_invoice = string, total = numeric'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
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
                $q->whereNull('company_id')->orWhereIn('company_id', auth()->user()?->companies()->select('companies.id') ?? []);
            });
        }
        return $query;
    }

    public static function getPages(): array
    {
        return ['index' => ListDocumentTypes::route('/'), 'create' => CreateDocumentType::route('/create'), 'edit' => EditDocumentType::route('/{record}/edit')];
    }
}

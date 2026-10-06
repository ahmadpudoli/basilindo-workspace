<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
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
            FileUpload::make('upload')->label('File dokumen')->required()->storeFiles(false)->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(51200),
            TextInput::make('title')->label('Judul')->required()->maxLength(255),
            Select::make('company_id')->label('Perusahaan')->relationship('company', 'name')->searchable()->preload()->required(),
            Select::make('project_id')->label('Proyek')->relationship('project', 'name')->searchable()->preload(),
            Select::make('vendor_id')->label('Vendor')->relationship('vendor', 'name')->searchable()->preload(),
            Select::make('document_type_id')->label('Jenis dokumen')->relationship('documentType', 'name')->searchable()->preload()->required(),
            TextInput::make('reference_number')->label('Nomor referensi')->maxLength(255),
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
                TextColumn::make('vendor.name')->label('Vendor')->searchable(),
                TextColumn::make('documentType.name')->label('Jenis')->badge(),
                TextColumn::make('reference_number')->label('Nomor referensi')->searchable(),
                TextColumn::make('document_date')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Perusahaan')->relationship('company', 'name')->searchable(),
                SelectFilter::make('document_type_id')->label('Jenis dokumen')->relationship('documentType', 'name'),
                SelectFilter::make('status')->options(['pending' => 'Menunggu', 'ready' => 'Siap', 'rejected' => 'Ditolak']),
                Filter::make('document_date')->form([DatePicker::make('from')->label('Dari'), DatePicker::make('until')->label('Sampai')])->query(fn (Builder $query, array $data) => $query->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('document_date', '>=', $date))->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('document_date', '<=', $date))),
            ])
            ->recordActions([ViewAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDocuments::route('/'), 'create' => CreateDocument::route('/create')];
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DocumentQueryService::class)->visibleTo(auth()->user());
    }
}

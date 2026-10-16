<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->modalHeading(fn ($record): string => 'Preview: '.$record->original_filename)
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(fn ($record) => view('filament.documents.preview', [
                    'document' => $record,
                    'previewUrl' => route('documents.preview', $record),
                ])),
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi dokumen')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('title')->label('Judul'),
                    TextEntry::make('description')->label('Deskripsi Dokumen')->placeholder('Belum diisi')->columnSpanFull(),
                    TextEntry::make('status')->label('Status')->badge(),
                    TextEntry::make('company.name')->label('Perusahaan'),
                    TextEntry::make('project.name')->label('Proyek')->placeholder('Belum diatur'),
                    TextEntry::make('documentType.name')->label('Jenis dokumen'),
                    TextEntry::make('reference_number')->label('Nomor referensi')->placeholder('Belum diatur'),
                    TextEntry::make('document_date')->label('Tanggal dokumen')->date('d/m/Y')->placeholder('Belum diatur'),
                    TextEntry::make('amount')->label('Nominal')->placeholder('Belum diatur'),
                ]),
                TextEntry::make('tags')->label('Tag')->formatStateUsing(fn ($state): string => is_array($state) ? implode(', ', $state) : (string) $state)->placeholder('Belum ada'),
                ViewEntry::make('metadata')
                    ->label('Metadata')
                    ->view('filament.documents.metadata-table', function (Document $record): array {
                        $metadata = is_array($record->metadata) ? $record->metadata : [];
                        $definitions = $record->documentType?->metadataDefinitions() ?? [];
                        $fieldNames = array_values(array_unique([...array_keys($definitions), ...array_keys($metadata)]));

                        return [
                            'rows' => collect($fieldNames)
                                ->map(fn (string $field): array => [
                                    'label' => str($field)->headline()->toString(),
                                    'value' => $metadata[$field] ?? null,
                                ])
                                ->all(),
                        ];
                    })
                    ->columnSpanFull(),
            ]),
            Section::make('File dan lifecycle')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('original_filename')->label('Nama file asli'),
                    TextEntry::make('currentVersion.mime_type')->label('MIME type'),
                    TextEntry::make('currentVersion.file_size')->label('Ukuran')->numeric(),
                    TextEntry::make('currentVersion.checksum')->label('Checksum'),
                    TextEntry::make('quarantined_at')->label('Masuk quarantine')->dateTime('d/m/Y H:i'),
                    TextEntry::make('released_at')->label('Dirilis')->dateTime('d/m/Y H:i')->placeholder('Belum'),
                    TextEntry::make('rejected_at')->label('Ditolak')->dateTime('d/m/Y H:i')->placeholder('Belum'),
                    TextEntry::make('retention_until')->label('Retensi sampai')->date('d/m/Y')->placeholder('Tidak diatur'),
                    TextEntry::make('legal_hold')->label('Legal hold')->badge(),
                ]),
                TextEntry::make('rejection_reason')->label('Alasan penolakan')->placeholder('Tidak ada'),
            ]),
        ]);
    }
}

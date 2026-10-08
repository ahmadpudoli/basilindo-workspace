<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
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
                ->url(fn ($record): string => route('documents.preview', $record), shouldOpenInNewTab: true),
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi dokumen')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('title')->label('Judul'),
                    TextEntry::make('status')->label('Status')->badge(),
                    TextEntry::make('company.name')->label('Perusahaan'),
                    TextEntry::make('project.name')->label('Proyek')->placeholder('Belum diatur'),
                    TextEntry::make('documentType.name')->label('Jenis dokumen'),
                    TextEntry::make('reference_number')->label('Nomor referensi')->placeholder('Belum diatur'),
                    TextEntry::make('document_date')->label('Tanggal dokumen')->date('d/m/Y')->placeholder('Belum diatur'),
                    TextEntry::make('amount')->label('Nominal')->placeholder('Belum diatur'),
                ]),
                TextEntry::make('tags')->label('Tag')->formatStateUsing(fn ($state): string => is_array($state) ? implode(', ', $state) : (string) $state)->placeholder('Belum ada'),
                TextEntry::make('metadata')->label('Metadata')->formatStateUsing(fn ($state): string => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : (string) $state)->prose(),
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

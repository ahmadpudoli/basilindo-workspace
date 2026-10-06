<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use IlluminateValidationValidationException;

class DocumentUploadService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function upload(UploadedFile $file, array $metadata, int $userId, string $disk = 's3'): Document
    {
        $document = new Document($metadata);
        $document->id = (string) Str::uuid();
        $document->uploaded_by = $userId;
        $document->original_filename = $file->getClientOriginalName();
        $document->status ??= 'pending';

        $checksum = hash_file('sha256', $file->getRealPath());
        $duplicate = DocumentVersion::where('checksum', $checksum)
            ->whereHas('document', fn ($query) => $query->where('company_id', $document->company_id)->whereNull('deleted_at'))
            ->exists();
        if ($duplicate) {
            throw ValidationException::withMessages(['upload' => 'Dokumen dengan isi file yang sama sudah terdaftar pada perusahaan ini.']);
        }

        $version = 1;
        $objectKey = sprintf(
            'companies/%s/documents/%s/v%s-%s.%s',
            $document->company_id,
            $document->id,
            $version,
            Str::random(16),
            $file->guessExtension() ?: 'bin',
        );

        $storage = Storage::disk($disk);
        $stored = false;

        try {
            $storage->putFileAs(dirname($objectKey), $file, basename($objectKey), ['visibility' => 'private']);
            $stored = true;

            return DB::transaction(function () use ($document, $file, $disk, $objectKey, $userId, $version, $checksum) {
                $document->save();
                DocumentVersion::create([
                    'document_id' => $document->id,
                    'version' => $version,
                    'disk' => $disk,
                    'object_key' => $objectKey,
                    'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'file_size' => $file->getSize() ?: 0,
                    'checksum' => $checksum,
                    'uploaded_by' => $userId,
                    'is_current' => true,
                ]);
                app(\App\Services\Audit\AuditLogger::class)->record('document.uploaded', $document, $document->company_id, [
                    'filename' => $document->original_filename, 'checksum' => $checksum,
                ]);

                return $document->fresh(['currentVersion']);
            });
        } catch (Throwable $exception) {
            if ($stored) {
                $storage->delete($objectKey);
            }

            throw $exception;
        }
    }
}

<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Validation\ValidationException;

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
        $document->status = 'quarantine';
        $document->quarantined_at = now();
        $document->reference_number = filled($document->reference_number) ? trim((string) $document->reference_number) : null;
        $document->normalized_reference_number = $this->normalizeReference($document->reference_number);
        $document->tags = $this->normalizeTags($document->tags);
        $this->validateRequiredMetadata($document);

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

    private function validateRequiredMetadata(Document $document): void
    {
        $required = $document->documentType?->required_fields ?? [];
        $custom = is_array($document->metadata) ? $document->metadata : [];
        $errors = [];

        foreach ($required as $field => $type) {
            $value = array_key_exists($field, $custom) ? $custom[$field] : $document->getAttribute($field);
            if ($value === null || $value === '') {
                $errors["metadata.$field"] = "Metadata wajib '$field' belum diisi.";
                continue;
            }

            $valid = match (strtolower((string) $type)) {
                'numeric', 'number', 'decimal' => is_numeric($value),
                'date' => strtotime((string) $value) !== false,
                'boolean', 'bool' => is_bool($value) || in_array($value, [0, 1, '0', '1'], true),
                'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
                default => is_scalar($value) && trim((string) $value) !== '',
            };

            if (! $valid) {
                $errors["metadata.$field"] = "Format metadata '$field' tidak sesuai tipe $type.";
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function normalizeReference(?string $reference): ?string
    {
        if (! $reference) {
            return null;
        }

        $normalized = preg_replace('/[^A-Z0-9]+/', '', strtoupper(trim($reference)));
        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeTags(mixed $tags): array
    {
        if (! is_array($tags)) {
            return [];
        }

        return collect($tags)
            ->map(fn ($tag) => strtolower(trim((string) $tag)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

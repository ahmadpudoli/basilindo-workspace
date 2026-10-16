<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use Core\Models\Company;
use Core\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class DocumentUploadService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function upload(UploadedFile $file, array $metadata, int $userId, string $disk = 's3'): Document
    {
        $metadata['amount'] = $this->normalizeAmountValue($metadata['amount'] ?? null);
        $document = new Document($metadata);
        $document->id = (string) Str::uuid();
        $document->uploaded_by = $userId;
        $document->original_filename = $file->getClientOriginalName();
        $document->status = 'quarantine';
        $document->quarantined_at = now();
        $document->reference_number = filled($document->reference_number) ? trim((string) $document->reference_number) : null;
        $document->normalized_reference_number = $this->normalizeReference($document->reference_number);
        $document->tags = $this->normalizeTags($document->tags);
        $this->normalizeAmount($document);
        $this->validateProjectCompanyRelation($document);
        $this->validateDocumentTypeCompanyRelation($document);
        $this->validateRequiredMetadata($document);

        $checksum = $this->calculateChecksum($file);
        $duplicate = DocumentVersion::where('checksum', $checksum)
            ->whereHas('document', fn ($query) => $query->where('company_id', $document->company_id)->whereNull('deleted_at'))
            ->exists();
        if ($duplicate) {
            throw ValidationException::withMessages(['upload' => 'Dokumen dengan isi file yang sama sudah terdaftar pada perusahaan ini.']);
        }

        $version = 1;
        $objectKey = $this->buildObjectKey($document, $version, $file);

        $storage = Storage::disk($disk);
        $stored = false;

        try {
            $stored = $this->storeFile($storage, $objectKey, $file);

            if (! $stored) {
                throw new \RuntimeException('File dokumen gagal disimpan ke object storage.');
            }

            $savedDocument = DB::transaction(function () use ($document, $file, $disk, $objectKey, $userId, $version, $checksum) {
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

            $this->deleteTemporaryUpload($file);

            return $savedDocument;
        } catch (Throwable $exception) {
            if ($stored) {
                $storage->delete($objectKey);
            }

            throw $exception;
        }
    }

    private function deleteTemporaryUpload(UploadedFile $file): void
    {
        if (! $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            return;
        }

        try {
            $file->delete();
        } catch (Throwable) {
            // Cleanup terjadwal Livewire tetap menjadi fallback bila delete gagal.
        }
    }

    private function validateRequiredMetadata(Document $document): void
    {
        $required = $document->documentType?->metadataDefinitions() ?? [];
        $custom = is_array($document->metadata) ? $document->metadata : [];
        $errors = [];

        foreach ($required as $field => $config) {
            $type = strtolower((string) ($config['type'] ?? 'string'));
            $mandatory = filter_var($config['mandatory'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $value = array_key_exists($field, $custom) ? $custom[$field] : $document->getAttribute($field);
            if ($mandatory && ($value === null || $value === '')) {
                $errors["metadata.$field"] = "Metadata wajib '$field' belum diisi.";

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $valid = match ($type) {
                'numeric' => DocumentMetadataValueNormalizer::isValidNumeric(DocumentMetadataValueNormalizer::numeric($value, 'numeric'), 'numeric'),
                'numeric_decimal' => DocumentMetadataValueNormalizer::isValidNumeric(DocumentMetadataValueNormalizer::numeric($value, 'numeric_decimal'), 'numeric_decimal'),
                'date' => strtotime((string) $value) !== false,
                'datetime' => strtotime((string) $value) !== false,
                'string' => is_scalar($value) && trim((string) $value) !== '',
                default => false,
            };

            if (! $valid) {
                $errors["metadata.$field"] = "Format metadata '$field' tidak sesuai tipe $type.";
            } elseif (in_array($type, ['numeric', 'numeric_decimal'], true)) {
                $custom[$field] = DocumentMetadataValueNormalizer::numeric($value, $type);
            }
        }

        $document->metadata = $custom;

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function calculateChecksum(UploadedFile $file): string
    {
        $stream = $this->openFileStream($file);

        try {
            $context = hash_init('sha256');
            hash_update_stream($context, $stream);

            return hash_final($context);
        } finally {
            fclose($stream);
        }
    }

    private function storeFile(object $storage, string $objectKey, UploadedFile $file): bool
    {
        $stream = $this->openFileStream($file);

        try {
            return $storage->writeStream($objectKey, $stream, ['visibility' => 'private']);
        } finally {
            fclose($stream);
        }
    }

    /** @return resource */
    private function openFileStream(UploadedFile $file)
    {
        if (method_exists($file, 'readStream')) {
            try {
                $stream = $file->readStream();

                if (is_resource($stream)) {
                    return $stream;
                }
            } catch (Throwable) {
                // Fall through to the local-path check for a useful message.
            }
        }

        $path = $file->getRealPath();

        if (is_string($path) && is_file($path) && is_readable($path)) {
            $stream = fopen($path, 'rb');

            if (is_resource($stream)) {
                return $stream;
            }
        }

        throw ValidationException::withMessages([
            'upload' => 'File sementara upload sudah tidak tersedia. Silakan pilih file kembali lalu coba lagi.',
        ]);
    }

    private function validateProjectCompanyRelation(Document $document): void
    {
        if (blank($document->project_id)) {
            return;
        }

        $belongsToCompany = Project::query()
            ->whereKey($document->project_id)
            ->where('company_id', $document->company_id)
            ->exists();

        if (! $belongsToCompany) {
            throw ValidationException::withMessages([
                'project_id' => 'Proyek yang dipilih harus berasal dari perusahaan internal yang dipilih.',
            ]);
        }
    }

    private function normalizeAmount(Document $document): void
    {
        $document->amount = $this->normalizeAmountValue($document->amount);
    }

    private function normalizeAmountValue(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $amount = DocumentMetadataValueNormalizer::numeric($value, 'numeric_decimal');

        if (! DocumentMetadataValueNormalizer::isValidNumeric($amount, 'numeric_decimal') || (float) $amount < 0) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal harus berupa angka desimal yang valid dan tidak boleh negatif.',
            ]);
        }

        return (string) $amount;
    }

    private function buildObjectKey(Document $document, int $version, UploadedFile $file): string
    {
        $companyStorageKey = Company::query()->whereKey($document->company_id)->value('storage_key');
        if (blank($companyStorageKey)) {
            throw ValidationException::withMessages([
                'company_id' => 'Perusahaan internal belum memiliki identifier storage. Jalankan migration perusahaan terlebih dahulu.',
            ]);
        }
        $documentDate = $document->document_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $prefix = config('storage.application_prefixes.document_management', 'app-document-management');

        return sprintf(
            '%s/%s/%s/documents/%s/v%s-%s.%s',
            trim((string) $prefix, '/'),
            $companyStorageKey,
            $documentDate,
            $document->id,
            $version,
            Str::random(16),
            $file->guessExtension() ?: 'bin',
        );
    }

    private function validateDocumentTypeCompanyRelation(Document $document): void
    {
        $belongsToCompany = DocumentType::query()
            ->whereKey($document->document_type_id)
            ->where('is_active', true)
            ->where(function ($query) use ($document): void {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $document->company_id);
            })
            ->exists();

        if (! $belongsToCompany) {
            throw ValidationException::withMessages([
                'document_type_id' => 'Jenis dokumen harus berstatus aktif dan berlaku untuk perusahaan internal yang dipilih.',
            ]);
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

<?php

namespace App\Services\Bundles;

use App\Models\Bundle;
use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use ZipArchive;
use App\Services\Documents\DocumentQueryService;
use App\Models\User;

class DocumentBundleService
{
    /** @param array<int, Document> $documents */
    public function create(array $documents, int $userId, string $disk = 's3'): Bundle
    {
        $documents = Collection::make($documents)->values();

        if ($documents->isEmpty()) {
            throw new InvalidArgumentException('Minimal satu dokumen diperlukan untuk bundling.');
        }

        if ($documents->pluck('company_id')->unique()->count() !== 1) {
            throw new InvalidArgumentException('Dokumen dari perusahaan berbeda tidak dapat dibundel bersama.');
        }

        return Bundle::create([
            'company_id' => $documents->first()->company_id,
            'requested_by' => $userId,
            'status' => 'queued',
            'criteria' => ['document_ids' => $documents->pluck('id')->values()->all()],
            'disk' => $disk,
            'document_count' => $documents->count(),
            'expires_at' => now()->addHours(24),
        ]);
    }

    public function generate(Bundle $bundle): Bundle
    {
        $ids = $bundle->criteria['document_ids'] ?? [];
        $documents = Document::with('currentVersion')->whereIn('id', $ids)->where('company_id', $bundle->company_id)->get();

        if ($documents->count() !== count($ids)) {
            throw new RuntimeException('Sebagian dokumen bundle tidak ditemukan atau tidak berwenang.');
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'file-organizer-bundle-');
        $zip = new ZipArchive();

        if ($zipPath === false || $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat file ZIP.');
        }

        $temporaryFiles = [];

        try {
            $manifest = [];
            foreach ($documents as $document) {
                $version = $document->currentVersion;
                if (! $version) {
                    continue;
                }

                $source = Storage::disk($version->disk)->readStream($version->object_key);
                if (! is_resource($source)) {
                    throw new RuntimeException('Object dokumen tidak dapat dibaca.');
                }

                $temporaryFile = tempnam(sys_get_temp_dir(), 'file-organizer-doc-');
                $target = fopen($temporaryFile, 'wb');
                stream_copy_to_stream($source, $target);
                fclose($target);
                fclose($source);
                $temporaryFiles[] = $temporaryFile;

                $filename = sprintf('%s-%s', $document->id, $document->original_filename);
                $zip->addFile($temporaryFile, $filename);
                $manifest[] = ['document_id' => $document->id, 'filename' => $document->original_filename, 'version' => $version->version];
            }

            $zip->addFromString('manifest.json', json_encode(['generated_at' => now()->toIso8601String(), 'documents' => $manifest], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            $zip->close();

            $objectKey = 'bundles/'.$bundle->company_id.'/'.$bundle->id.'.zip';
            $stream = fopen($zipPath, 'rb');
            Storage::disk($bundle->disk)->writeStream($objectKey, $stream);
            fclose($stream);

            $result = tap($bundle)->update(['status' => 'completed', 'object_key' => $objectKey, 'document_count' => count($manifest), 'completed_at' => now()]);
            app(\App\Services\Audit\AuditLogger::class)->record('bundle.generated', $result, $result->company_id, ['document_count' => count($manifest)]);
            return $result;
        } finally {
            if (is_resource($zip)) {
                $zip->close();
            }
            @unlink($zipPath);
            foreach ($temporaryFiles as $temporaryFile) {
                @unlink($temporaryFile);
            }
        }
    }

    public function createFromFilters(array $filters, User $user, string $disk = 's3'): Bundle
    {
        $documents = app(DocumentQueryService::class)->apply(
            app(DocumentQueryService::class)->visibleTo($user),
            $filters,
        )->with('currentVersion')->get()->all();
        return $this->create($documents, $user->id, $disk);
    }
}

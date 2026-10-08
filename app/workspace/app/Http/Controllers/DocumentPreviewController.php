<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\Audit\AuditLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentPreviewController extends Controller
{
    public function __invoke(Document $document): StreamedResponse
    {
        Gate::authorize('preview', $document);
        $version = $document->currentVersion;
        abort_unless($version, 404);
        abort_unless(in_array($version->mime_type, ['application/pdf', 'image/jpeg', 'image/png'], true), 415);

        $stream = Storage::disk($version->disk)->readStream($version->object_key);
        abort_unless(is_resource($stream), 404);

        app(AuditLogger::class)->record('document.previewed', $document, $document->company_id, [
            'mime_type' => $version->mime_type,
        ]);

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $version->mime_type,
            'Content-Disposition' => 'inline; filename="'.addslashes($document->original_filename).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

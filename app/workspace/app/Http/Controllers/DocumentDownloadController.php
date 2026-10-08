<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(Document $document): StreamedResponse
    {
        Gate::authorize('download', $document);
        $version = $document->currentVersion;
        abort_unless($version, 404);

        $stream = Storage::disk($version->disk)->readStream($version->object_key);
        abort_unless(is_resource($stream), 404);

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            fclose($stream);
        }, $document->original_filename, ['Content-Type' => $version->mime_type]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BundleDownloadController extends Controller
{
    public function __invoke(Bundle $bundle): StreamedResponse
    {
        Gate::authorize('download', $bundle);
        $stream = Storage::disk($bundle->disk)->readStream($bundle->object_key);
        abort_unless(is_resource($stream), 404);

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            fclose($stream);
        }, 'file-organizer-bundle-'.$bundle->id.'.zip', ['Content-Type' => 'application/zip']);
    }
}

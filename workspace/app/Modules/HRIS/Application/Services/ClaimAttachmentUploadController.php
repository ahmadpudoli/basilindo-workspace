<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Claim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClaimAttachmentUploadController
{
    public function store(Request $request, ?Claim $claim = null): JsonResponse
    {
        if ($claim) {
            Gate::authorize('update', $claim);
            abort_unless(in_array($claim->status, ['draft', 'needs_revision'], true), 404);
        } else {
            Gate::authorize('create', Claim::class);
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp,heic,heif', 'max:10240'],
        ], [
            'file.required' => 'Pilih file bukti nota terlebih dahulu.',
            'file.mimes' => 'File bukti harus berupa PDF atau gambar JPG, PNG, WEBP, HEIC.',
            'file.max' => 'Ukuran file bukti maksimal 10 MB.',
        ]);

        $token = (string) Str::ulid();
        $directory = 'hris-claim-tmp/'.(string) $request->user()->getKey();
        $stored = Storage::disk((string) config('hris.claim_attachment_disk', 's3'))->putFileAs($directory, $validated['file'], $token, [
            'visibility' => 'private',
            'ContentType' => $validated['file']->getMimeType() ?: 'application/octet-stream',
        ]);
        abort_unless($stored, 500, 'File bukti gagal disimpan ke penyimpanan sementara.');

        return response()->json(['token' => $token, 'name' => $validated['file']->getClientOriginalName()]);
    }
}

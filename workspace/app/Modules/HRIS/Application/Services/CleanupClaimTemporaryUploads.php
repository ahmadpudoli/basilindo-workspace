<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupClaimTemporaryUploads extends Command
{
    protected $signature = 'hris:cleanup-claim-temp-uploads {--dry-run : Hanya menampilkan file yang akan dihapus}';

    protected $description = 'Membersihkan bukti reimbursement sementara yang kedaluwarsa dari MinIO';

    public function handle(): int
    {
        $disk = Storage::disk((string) config('hris.claim_attachment_disk', 's3'));
        $directory = 'hris-claim-tmp';
        $cutoff = now()->subMinutes(max(1, (int) config('hris.claim_attachment_temp_ttl_minutes', 30)))->timestamp;
        $deleted = 0;

        foreach ($disk->allFiles($directory) as $path) {
            if (! str_starts_with($path, $directory.'/') || $disk->lastModified($path) >= $cutoff) continue;
            if ($this->option('dry-run')) {
                $this->line($path);
                continue;
            }
            if ($disk->delete($path)) $deleted++;
        }

        $this->info($this->option('dry-run') ? 'Dry-run cleanup bukti reimbursement selesai.' : "{$deleted} file sementara dihapus.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Modules\DocumentManagement\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTemporaryUploads extends Command
{
    protected $signature = 'workspace:cleanup-temp-uploads {--dry-run : Hanya menampilkan object yang akan dihapus}';

    protected $description = 'Menghapus temporary upload Workspace yang sudah melewati TTL';

    public function handle(): int
    {
        $diskName = (string) config('livewire.temporary_file_upload.disk', 's3');
        $directory = trim((string) config('livewire.temporary_file_upload.directory', 'workspace-tmp'), '/');
        $ttlHours = max(1, (int) env('WORKSPACE_TEMPORARY_UPLOAD_TTL_HOURS', 24));
        $cutoff = now()->subHours($ttlHours)->timestamp;
        $disk = Storage::disk($diskName);
        $deleted = 0;

        foreach ($disk->allFiles($directory) as $path) {
            if ($disk->lastModified($path) >= $cutoff) {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line($path);
                continue;
            }

            if ($disk->delete($path)) {
                $deleted++;
            }
        }

        $this->info($this->option('dry-run')
            ? 'Dry-run cleanup temporary upload selesai.'
            : "{$deleted} temporary upload dihapus.");

        return self::SUCCESS;
    }
}

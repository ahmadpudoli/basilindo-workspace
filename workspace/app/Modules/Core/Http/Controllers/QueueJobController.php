<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Domain\Models\QueueJobLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class QueueJobController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_queue_jobs');
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'queue' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['queued', 'processing', 'succeeded', 'failed'])],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'until' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', Rule::in([10, 25, 50, 100])],
            'sort' => ['nullable', Rule::in(['job_name', 'queue_name', 'status', 'queued_at', 'started_at', 'finished_at', 'attempts'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $filters = array_merge(['q' => '', 'queue' => '', 'status' => '', 'from' => '', 'until' => ''], $filters);
        foreach (['q', 'queue', 'status', 'from', 'until'] as $key) {
            $filters[$key] = (string) ($filters[$key] ?? '');
        }
        $perPage = (int) ($filters['per_page'] ?? 10);
        $sort = $filters['sort'] ?? 'queued_at';
        $direction = $filters['direction'] ?? 'desc';

        $filteredQuery = QueueJobLog::query();
        $this->applyFilters($filteredQuery, $filters, includeStatus: false);
        $counts = (clone $filteredQuery)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $query = clone $filteredQuery;
        $this->applyFilters($query, $filters, includeStatus: true);
        $jobs = $query->orderBy($sort, $direction)->orderByDesc('id')->paginate($perPage)->withQueryString();
        $queues = QueueJobLog::query()->whereNotNull('queue_name')->where('queue_name', '<>', '')
            ->distinct()->orderBy('queue_name')->pluck('queue_name');
        $defaultQueue = (string) config('queue.connections.'.config('queue.default').'.queue', 'default');
        return view('modules.core.queue-jobs', [
            'jobs' => $jobs,
            'filters' => $filters,
            'perPage' => $perPage,
            'sort' => $sort,
            'direction' => $direction,
            'queues' => $queues,
            'counts' => $counts,
            'totalCount' => (clone $filteredQuery)->count(),
            'queueSize' => Queue::size($defaultQueue),
            'queueName' => $defaultQueue,
        ]);
    }

    public function retry(QueueJobLog $queueJobLog): RedirectResponse
    {
        Gate::authorize('retry_queue_jobs');

        abort_unless(
            $queueJobLog->status === 'failed'
                && $queueJobLog->attempts >= ($queueJobLog->max_attempts ?? (int) config('workers.max_attempts', 3)),
            404,
        );

        $failedJob = $this->resolveFailedJob(app('queue.failer'), $queueJobLog);
        if ($failedJob && blank($queueJobLog->failed_job_uuid)) {
            $queueJobLog->failed_job_uuid = $failedJob->uuid ?? json_decode($failedJob->payload, true)['uuid'] ?? null;
            $queueJobLog->max_attempts ??= (int) config('workers.max_attempts', 3);
            $queueJobLog->save();
        }
        abort_unless($this->failedPayloadMatchesLog($failedJob, $queueJobLog), 404, 'Payload job gagal tidak ditemukan.');

        $exitCode = Artisan::call('queue:retry', ['id' => [$queueJobLog->failed_job_uuid]]);
        if ($exitCode !== 0) {
            return back()->with('error', 'Job gagal dimasukkan kembali ke antrean.');
        }

        return back()->with('status', 'Job berhasil dimasukkan kembali ke antrean.');
    }

    private function failedPayloadMatchesLog(?object $failedJob, QueueJobLog $log): bool
    {
        if (! $failedJob) {
            return false;
        }

        $payload = json_decode($failedJob->payload, true);

        return is_array($payload)
            && (string) ($payload['uuid'] ?? '') === (string) $log->failed_job_uuid
            && (string) ($payload['displayName'] ?? '') === (string) $log->job_name
            && ($failedJob->queue ?? null) === $log->queue_name;
    }

    private function resolveFailedJob(object $provider, QueueJobLog $log): ?object
    {
        if (filled($log->failed_job_uuid)) {
            $failedJob = $provider->find($log->failed_job_uuid);
            if ($this->failedPayloadMatchesLog($failedJob, $log)) {
                return $failedJob;
            }
        }

        // Older queue logs predate failed UUID tracking. Match the failed payload by
        // its immutable job identity and nearest failure timestamp to recover the link.
        $matches = collect($provider->all())->filter(function (object $failedJob) use ($log): bool {
            $payload = json_decode($failedJob->payload, true);
            return is_array($payload)
                && (string) ($payload['displayName'] ?? '') === (string) $log->job_name
                && ($failedJob->queue ?? null) === $log->queue_name;
        });

        if ($matches->isEmpty() || ! $log->finished_at) {
            return null;
        }

        $match = $matches->sortBy(function (object $failedJob) use ($log): int {
            return abs(strtotime((string) $failedJob->failed_at) - $log->finished_at->getTimestamp());
        })->first();

        return abs(strtotime((string) $match->failed_at) - $log->finished_at->getTimestamp()) <= 600
            ? $match
            : null;
    }

    private function applyFilters(Builder $query, array $filters, bool $includeStatus): void
    {
        if ($filters['q'] !== '') {
            $search = '%'.addcslashes($filters['q'], '%_\\').'%';
            $query->where(fn (Builder $nested) => $nested
                ->where('job_name', 'ilike', $search)
                ->orWhere('job_id', 'ilike', $search));
        }

        if ($filters['queue'] !== '') {
            $query->where('queue_name', $filters['queue']);
        }

        if ($includeStatus && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['from'] !== '') {
            $query->whereDate('queued_at', '>=', $filters['from']);
        }

        if ($filters['until'] !== '') {
            $query->whereDate('queued_at', '<=', $filters['until']);
        }
    }
}

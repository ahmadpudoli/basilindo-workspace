<?php

namespace App\Modules\Core\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Notifications\Jobs\SendEmailNotification;
use App\Modules\Core\Notifications\Models\EmailNotificationLog;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EmailQueueController extends Controller
{
    public function retry(EmailNotificationLog $emailNotificationLog, DatabaseManager $database): RedirectResponse
    {
        Gate::authorize('retry_queue_jobs');

        $requeued = $database->connection($emailNotificationLog->getConnectionName())->transaction(function () use ($emailNotificationLog): bool {
            $log = EmailNotificationLog::query()->whereKey($emailNotificationLog->getKey())->lockForUpdate()->firstOrFail();

            if ($log->status !== 'failed' || $log->attempts < SendEmailNotification::MAX_ATTEMPTS) {
                return false;
            }

            $log->update([
                'status' => 'queued',
                'queued_at' => now(),
                'sent_at' => null,
                'error_message' => null,
            ]);

            SendEmailNotification::dispatch($log->getKey())->afterCommit();

            return true;
        });

        return $requeued
            ? back()->with('status', 'Email berhasil dimasukkan kembali ke antrean.')
            : back()->with('error', 'Email tidak dapat diantrikan ulang karena statusnya sudah berubah atau percobaan maksimal belum tercapai.');
    }

    public function index(Request $request): View
    {
        Gate::authorize('view_email_queue');
        $statuses = ['queued', 'processing', 'sent', 'failed'];
        $status = $request->string('status')->toString();
        $perPage = $request->integer('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) $perPage = 10;
        $filters = [
            'q' => mb_substr(trim($request->string('q')->toString()), 0, 120),
            'status' => in_array($status, $statuses, true) ? $status : '',
            'from' => $request->string('from')->toString(),
            'until' => $request->string('until')->toString(),
        ];
        $request->validate(['from' => ['nullable', 'date'], 'until' => ['nullable', 'date']]);
        $sortColumns = ['recipient', 'subject', 'template', 'status', 'attempts', 'queued_at', 'sent_at'];
        $sort = $request->string('sort')->toString();
        if (! in_array($sort, $sortColumns, true)) $sort = 'queued_at';
        $direction = $request->string('direction')->toString();
        if (! in_array($direction, ['asc', 'desc'], true)) $direction = 'desc';
        $query = EmailNotificationLog::query()->orderBy($sort, $direction)->orderByDesc('id');
        if ($filters['q'] !== '') {
            $query->where(function ($builder) use ($filters): void {
                $term = '%'.$filters['q'].'%';
                $builder->where('recipient', 'ilike', $term)
                    ->orWhere('subject', 'ilike', $term)
                    ->orWhere('template', 'ilike', $term);
            });
        }
        if ($filters['status'] !== '') $query->where('status', $filters['status']);
        if ($filters['from'] !== '') $query->whereDate('queued_at', '>=', $filters['from']);
        if ($filters['until'] !== '') $query->whereDate('queued_at', '<=', $filters['until']);
        $totalCount = (clone $query)->reorder()->count();

        return view('modules.core.email-queue', [
            'logs' => $query->paginate($perPage)->withQueryString(),
            'status' => $filters['status'],
            'filters' => $filters,
            'statuses' => $statuses,
            'perPage' => $perPage,
            'sort' => $sort,
            'direction' => $direction,
            'totalCount' => $totalCount,
            'counts' => EmailNotificationLog::query()->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status'),
        ]);
    }
}

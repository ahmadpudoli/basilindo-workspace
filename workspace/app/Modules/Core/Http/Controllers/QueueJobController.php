<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Domain\Models\QueueJobLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\View\View;

class QueueJobController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_queue_jobs');
        $status = $request->string('status')->toString();
        $allowedStatuses = ['queued', 'processing', 'succeeded', 'failed'];
        $query = QueueJobLog::query()->latest('created_at');
        if (in_array($status, $allowedStatuses, true)) $query->where('status', $status);
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $jobs = $query->paginate($perPage)->withQueryString();
        $defaultQueue = (string) config('queue.connections.'.config('queue.default').'.queue', 'default');
        return view('modules.core.queue-jobs', [
            'jobs' => $jobs,
            'status' => $status,
            'counts' => QueueJobLog::query()->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'queueSize' => Queue::size($defaultQueue),
            'queueName' => $defaultQueue,
        ]);
    }
}

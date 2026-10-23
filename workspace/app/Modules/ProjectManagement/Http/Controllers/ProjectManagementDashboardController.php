<?php

namespace App\Modules\ProjectManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ProjectManagement\Domain\Models\Epic;
use App\Modules\ProjectManagement\Domain\Models\Ticket;
use Core\Models\Project;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class ProjectManagementDashboardController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('viewAny', Project::class);
        return view('modules.project-management.dashboard', [
            'stats' => [
                ['label' => 'Project', 'value' => Project::query()->count()],
                ['label' => 'Ticket', 'value' => Ticket::query()->count()],
                ['label' => 'Epic', 'value' => Epic::query()->count()],
                ['label' => 'Ticket terbuka', 'value' => Ticket::query()->where(function ($query): void {
                    $query->whereDoesntHave('status')->orWhereHas('status', fn ($status) => $status->where('is_completed', false));
                })->count()],
            ],
            'tickets' => Ticket::query()->with(['project', 'status', 'priority'])->latest()->limit(10)->get(),
        ]);
    }
}

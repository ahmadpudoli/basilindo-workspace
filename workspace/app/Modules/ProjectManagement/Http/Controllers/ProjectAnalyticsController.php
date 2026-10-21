<?php

namespace App\Modules\ProjectManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ProjectManagement\Domain\Models\TicketComment;
use App\Modules\ProjectManagement\Domain\Models\TicketHistory;
use Core\Models\User;
use Illuminate\View\View;

class ProjectAnalyticsController extends Controller
{
    public function contributions(): View
    {
        abort_unless(auth()->check(), 401);
        $users = User::query()->orderBy('name')->get()->map(fn (User $user) => [
            'user' => $user,
            'comments' => TicketComment::query()->where('user_id', $user->getKey())->count(),
            'status_updates' => TicketHistory::query()->where('user_id', $user->getKey())->count(),
        ]);

        return view('modules.project-management.analytics', ['title' => 'Kontribusi pengguna', 'users' => $users, 'leaderboard' => false]);
    }

    public function leaderboard(): View
    {
        abort_unless(auth()->check(), 401);
        $users = User::query()->orderBy('name')->get()->map(fn (User $user) => [
            'user' => $user,
            'comments' => TicketComment::query()->where('user_id', $user->getKey())->count(),
            'status_updates' => TicketHistory::query()->where('user_id', $user->getKey())->count(),
        ])->sortByDesc(fn (array $item) => $item['comments'] + $item['status_updates'])->values();

        return view('modules.project-management.analytics', ['title' => 'Leaderboard', 'users' => $users, 'leaderboard' => true]);
    }
}

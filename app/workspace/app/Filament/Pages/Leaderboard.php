<?php

namespace App\Filament\Pages;

use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Core\Models\User;
use Filament\Pages\Page;

class Leaderboard extends Page
{
    use HasWorkspaceModuleNavigation;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?string $navigationLabel = 'Leaderboard';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'leaderboard';
    protected string $view = 'filament.pages.leaderboard';

    public function getViewData(): array
    {
        $users = User::query()->orderBy('name')->get()->map(function (User $user): array {
            return ['user' => $user, 'comments' => TicketComment::where('user_id', $user->getKey())->count(), 'updates' => TicketHistory::where('user_id', $user->getKey())->count()];
        })->sortByDesc(fn (array $item): int => $item['comments'] + $item['updates'])->values();
        return compact('users');
    }
}

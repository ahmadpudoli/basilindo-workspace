<?php

namespace App\Filament\Pages;

use App\Models\TicketComment;
use App\Models\TicketHistory;
use Core\Models\User;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Filament\Pages\Page;

class UserContributions extends Page
{
    use HasWorkspaceModuleNavigation;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?string $navigationLabel = 'User Contributions';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'user-contributions';
    protected string $view = 'filament.pages.user-contributions';

    public function getViewData(): array
    {
        $users = User::query()->orderBy('name')->get()->map(function (User $user): array {
            return ['user' => $user, 'comments' => TicketComment::where('user_id', $user->getKey())->count(), 'status_updates' => TicketHistory::where('user_id', $user->getKey())->count()];
        });
        return compact('users');
    }
}

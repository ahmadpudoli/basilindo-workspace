<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Core\Models\Project;
use Filament\Pages\Page;

class ProjectBoard extends Page
{
    use HasWorkspaceModuleNavigation;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?string $navigationLabel = 'Project Board';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'project-board';
    protected string $view = 'filament.pages.project-board';

    public function getViewData(): array
    {
        $projects = Project::query()->orderBy('name')->get();
        $projectId = request()->integer('project_id') ?: $projects->first()?->getKey();
        $statuses = TicketStatus::query()->where(fn ($query) => $query->whereNull('project_id')->orWhere('project_id', $projectId))->orderBy('sort_order')->get();
        $tickets = Ticket::query()->with(['status', 'priority', 'assignees'])->when($projectId, fn ($query) => $query->where('project_id', $projectId))->orderByDesc('created_at')->get();

        return compact('projects', 'projectId', 'statuses', 'tickets');
    }
}

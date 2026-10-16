<?php

namespace App\Filament\Pages;

use Core\Models\Project;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Filament\Pages\Page;

class ProjectTimeline extends Page
{
    use HasWorkspaceModuleNavigation;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?string $navigationLabel = 'Project Timeline';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'project-timeline';
    protected string $view = 'filament.pages.project-timeline';

    public function getViewData(): array
    {
        return ['projects' => Project::query()->withCount('tickets')->whereNotNull('start_date')->whereNotNull('end_date')->orderBy('start_date')->get()];
    }
}

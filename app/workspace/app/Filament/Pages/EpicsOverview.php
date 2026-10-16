<?php

namespace App\Filament\Pages;

use App\Models\Epic;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Filament\Pages\Page;

class EpicsOverview extends Page
{
    use HasWorkspaceModuleNavigation;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';
    protected static string|\UnitEnum|null $navigationGroup = 'Analytics';
    protected static ?string $navigationLabel = 'Epic Overview';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'epics-overview';
    protected string $view = 'filament.pages.epics-overview';

    public function getViewData(): array
    {
        return ['epics' => Epic::query()->with('project')->withCount('tickets')->orderBy('sort_order')->orderBy('name')->get()];
    }
}

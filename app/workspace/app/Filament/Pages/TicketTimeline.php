<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Filament\Support\HasWorkspaceModuleNavigation;
use Filament\Pages\Page;

class TicketTimeline extends Page
{
    use HasWorkspaceModuleNavigation;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';
    protected static ?string $navigationLabel = 'Ticket Timeline';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'ticket-timeline';
    protected string $view = 'filament.pages.ticket-timeline';

    public function getViewData(): array
    {
        $tickets = Ticket::query()->with(['project', 'status', 'histories.status', 'histories.user'])->latest()->limit(50)->get();
        return compact('tickets');
    }
}

<?php

namespace App\Filament\Resources;

use App\Exports\TicketsExport;
use App\Filament\Actions\DownloadTicketTemplateAction;
use App\Filament\Actions\ExportTicketsAction;
use App\Filament\Actions\ImportTicketsAction;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ticket baru'),
            ImportTicketsAction::make(),
            DownloadTicketTemplateAction::make(),
            ExportTicketsAction::make(),
        ];
    }

    public function exportTickets(array $selectedColumns)
    {
        if ($selectedColumns === []) {
            return null;
        }

        $tickets = TicketResource::getEloquentQuery()
            ->with(['assignees', 'status', 'project', 'epic'])
            ->latest()
            ->get();

        return Excel::download(
            new TicketsExport($tickets, $selectedColumns),
            'tickets_'.Str::slug(now()->format('Y-m-d_H-i-s'), '_').'.xlsx',
        );
    }
}

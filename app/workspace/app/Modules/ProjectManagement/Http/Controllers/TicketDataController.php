<?php

namespace App\Modules\ProjectManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ProjectManagement\Domain\Models\Ticket;
use Core\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Gate;

class TicketDataController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Ticket::class);
        $tickets = Ticket::query()->with(['project', 'status', 'priority', 'assignees'])->latest()->get();

        return response()->streamDownload(function () use ($tickets): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['project_id', 'project', 'ticket_id', 'name', 'description', 'status', 'priority', 'assignees', 'start_date', 'due_date']);
            foreach ($tickets as $ticket) {
                fputcsv($output, [$ticket->project_id, $ticket->project?->name, $ticket->uuid, $ticket->name, $ticket->description, $ticket->status?->name, $ticket->priority?->name, $ticket->assignees->pluck('email')->implode('|'), $ticket->start_date?->toDateString(), $ticket->due_date?->toDateString()]);
            }
            fclose($output);
        }, 'tickets-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('create', Ticket::class);
        $data = $request->validate(['ticket_file' => 'required|file|mimes:csv,txt|max:5120']);
        $handle = fopen($data['ticket_file']->getRealPath(), 'rb');
        $headers = fgetcsv($handle);
        abort_unless($headers && in_array('project_id', $headers, true) && in_array('name', $headers, true), 422, 'CSV harus memiliki kolom project_id dan name.');
        $created = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn ($value) => $value !== null && $value !== '')) === 0) { continue; }
            $record = array_combine($headers, array_pad($row, count($headers), null));
            $project = Project::query()->findOrFail($record['project_id']);
            Gate::authorize('view', $project);
            validator($record, ['project_id' => 'required|exists:core_projects,id', 'name' => 'required|string|max:255', 'description' => 'nullable|string', 'start_date' => 'nullable|date', 'due_date' => 'nullable|date'])->validate();
            Ticket::create(['project_id' => $project->getKey(), 'name' => $record['name'], 'description' => $record['description'] ?? null, 'start_date' => $record['start_date'] ?? null, 'due_date' => $record['due_date'] ?? null]);
            $created++;
        }
        fclose($handle);
        return back()->with('status', "$created ticket berhasil diimport.");
    }
}

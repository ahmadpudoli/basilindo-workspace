<?php

namespace App\Modules\ProjectManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ProjectManagement\Domain\Models\Epic;
use App\Modules\ProjectManagement\Domain\Models\ProjectNote;
use App\Modules\ProjectManagement\Domain\Models\Ticket;
use App\Modules\ProjectManagement\Domain\Models\TicketPriority;
use App\Modules\ProjectManagement\Domain\Models\TicketStatus;
use App\Modules\ProjectManagement\Domain\Models\TicketComment;
use App\Modules\ProjectManagement\Domain\Models\Notification;
use Core\Models\User;
use Core\Models\Company;
use Core\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectManagementController extends Controller
{
    private const RESOURCES = ['projects' => [Project::class, 'Project'], 'tickets' => [Ticket::class, 'Ticket'], 'epics' => [Epic::class, 'Epic'], 'notes' => [ProjectNote::class, 'Catatan'], 'statuses' => [TicketStatus::class, 'Status ticket'], 'priorities' => [TicketPriority::class, 'Prioritas ticket'], 'comments' => [TicketComment::class, 'Komentar ticket'], 'notifications' => [Notification::class, 'Notifikasi']];
    public function index(Request $request, string $resource): View { [$model, $label] = $this->resource($resource); $this->authorizeResource($model); $column = $resource === 'comments' ? 'comment' : ($resource === 'notifications' ? 'title' : 'name'); $records = $model::query()->when($request->q, fn ($q, $value) => $q->where($column, 'ilike', "%{$value}%"))->latest()->paginate(15)->withQueryString(); return view('modules.project-management.resource-index', ['resource' => $resource, 'label' => $label, 'records' => $records, 'columns' => $this->columns($resource)]); }
    public function create(Request $request, string $resource): View { [$model, $label] = $this->resource($resource); $this->authorizeResource($model, 'create'); return view('modules.project-management.resource-form', array_merge(['resource' => $resource, 'label' => $label], $this->options($resource, $request->user()))); }
    public function store(Request $request, string $resource): RedirectResponse
    {
        [$model, $label] = $this->resource($resource);
        $this->authorizeResource($model, 'create');
        $data = $this->validated($request, $resource);
        $assignees = $data['assignees'] ?? [];
        unset($data['assignees']);
        if ($resource === 'comments') { $data['user_id'] = auth()->id(); }
        $record = $model::create($data);
        if ($resource === 'tickets') { $record->assignees()->sync($assignees); }
        return redirect()->route('workspace.pm.resource', $resource)->with('status', "$label berhasil dibuat.");
    }
    public function edit(Request $request, string $resource, string $id): View { [$model, $label] = $this->resource($resource); $record = $model::query()->findOrFail($id); $this->authorizeRecord($record); return view('modules.project-management.resource-form', array_merge(['resource' => $resource, 'label' => $label, 'record' => $record], $this->options($resource, $request->user()))); }
    public function update(Request $request, string $resource, string $id): RedirectResponse
    {
        [$model, $label] = $this->resource($resource);
        $record = $model::query()->findOrFail($id);
        $this->authorizeRecord($record, 'update');
        $data = $this->validated($request, $resource);
        $assignees = $data['assignees'] ?? [];
        unset($data['assignees']);
        $record->update($data);
        if ($resource === 'tickets') { $record->assignees()->sync($assignees); }
        return redirect()->route('workspace.pm.resource', $resource)->with('status', "$label berhasil diperbarui.");
    }
    public function destroy(string $resource, string $id): RedirectResponse { [$model, $label] = $this->resource($resource); $record = $model::query()->findOrFail($id); $this->authorizeRecord($record, 'delete'); $record->delete(); return back()->with('status', "$label dihapus."); }
    public function showProject(string $id): View { $project = Project::query()->with(['company', 'members', 'tickets.status', 'epics', 'notes', 'documents.documentType'])->findOrFail($id); Gate::authorize('view', $project); return view('modules.project-management.project-show', ['project' => $project, 'users' => User::query()->orderBy('name')->pluck('name', 'id')]); }
    public function addMember(Request $request, string $id): RedirectResponse { $project = Project::query()->findOrFail($id); Gate::authorize('update', $project); $data = $request->validate(['user_id' => 'required|exists:core_users,id']); $project->members()->syncWithoutDetaching([$data['user_id']]); return back()->with('status', 'Anggota project ditambahkan.'); }
    public function removeMember(string $id, string $userId): RedirectResponse { $project = Project::query()->findOrFail($id); Gate::authorize('update', $project); $project->members()->detach($userId); return back()->with('status', 'Anggota project dihapus.'); }
    private function resource(string $resource): array { abort_unless(isset(self::RESOURCES[$resource]), 404); return self::RESOURCES[$resource]; }
    private function authorizeResource(string $model, string $ability = 'viewAny'): void { if (in_array($model, [Project::class, Ticket::class], true)) Gate::authorize($ability, $model); else abort_unless(auth()->check(), 401); }
    private function authorizeRecord(object $record, string $ability = 'view'): void { if ($record instanceof Project || $record instanceof Ticket) Gate::authorize($ability, $record); else abort_unless(auth()->check(), 401); }
    private function options(string $resource, \Core\Models\User $user): array { return match ($resource) { 'projects' => ['companies' => Company::query()->operationalFor($user)->orderBy('name')->pluck('name', 'id')], 'tickets' => ['projects' => Project::query()->orderBy('name')->pluck('name', 'id'), 'statuses' => TicketStatus::query()->orderBy('sort_order')->pluck('name', 'id'), 'priorities' => TicketPriority::query()->orderBy('sort_order')->pluck('name', 'id'), 'users' => User::query()->orderBy('name')->pluck('name', 'id')], 'epics', 'notes' => ['projects' => Project::query()->orderBy('name')->pluck('name', 'id')], 'comments' => ['tickets' => Ticket::query()->orderBy('name')->pluck('name', 'id')], 'notifications' => ['users' => User::query()->orderBy('name')->pluck('name', 'id')], default => ['projects' => Project::query()->orderBy('name')->pluck('name', 'id')], }; }
    private function columns(string $resource): array { return match ($resource) { 'projects' => ['name', 'ticket_prefix', 'start_date', 'end_date'], 'tickets' => ['name', 'uuid', 'project_id', 'due_date'], 'epics' => ['name', 'project_id', 'start_date', 'end_date'], 'notes' => ['title', 'project_id', 'note_date'], 'comments' => ['ticket_id', 'user_id', 'comment'], 'notifications' => ['user_id', 'type', 'title', 'read_at'], default => ['name', 'color', 'sort_order'] }; }
    private function validated(Request $request, string $resource): array { return $request->validate(match ($resource) { 'projects' => ['company_id' => 'required|exists:core_companies,id', 'name' => 'required|string|max:255', 'description' => 'nullable|string', 'ticket_prefix' => 'nullable|string|max:20', 'color' => 'nullable|string|max:20', 'start_date' => 'nullable|date', 'end_date' => 'nullable|date'], 'tickets' => ['project_id' => 'required|exists:core_projects,id', 'ticket_status_id' => 'nullable|exists:ticket_statuses,id', 'priority_id' => 'nullable|exists:ticket_priorities,id', 'name' => 'required|string|max:255', 'description' => 'nullable|string', 'start_date' => 'nullable|date', 'due_date' => 'nullable|date', 'assignees' => 'nullable|array', 'assignees.*' => 'exists:core_users,id'], 'epics' => ['project_id' => 'required|exists:core_projects,id', 'name' => 'required|string|max:255', 'description' => 'nullable|string', 'start_date' => 'nullable|date', 'end_date' => 'nullable|date'], 'notes' => ['project_id' => 'required|exists:core_projects,id', 'title' => 'required|string|max:255', 'content' => 'required|string', 'note_date' => 'nullable|date'], 'comments' => ['ticket_id' => 'required|exists:tickets,id', 'comment' => 'required|string'], 'notifications' => ['user_id' => 'required|exists:core_users,id', 'type' => 'required|string|max:100', 'title' => 'required|string|max:255', 'message' => 'required|string', 'data' => 'nullable|json', 'read_at' => 'nullable|date'], default => ['name' => 'required|string|max:255', 'color' => 'nullable|string|max:20', 'sort_order' => 'nullable|integer'] }); }
    public function board(): View { Gate::authorize('viewAny', Ticket::class); $tickets = Ticket::query()->with(['project', 'status', 'priority'])->orderBy('due_date')->get()->groupBy(fn ($ticket) => $ticket->status?->name ?: 'Tanpa status'); return view('modules.project-management.board', compact('tickets')); }
    public function timeline(): View { Gate::authorize('viewAny', Ticket::class); $tickets = Ticket::query()->with(['project', 'status'])->whereNotNull('due_date')->orderBy('due_date')->paginate(30); return view('modules.project-management.timeline', compact('tickets')); }
}

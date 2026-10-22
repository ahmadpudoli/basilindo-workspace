<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DocumentManagement\Domain\Models\AuditEvent;
use Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAuditAccess();

        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'module' => trim((string) $request->input('module', '')),
            'actor_id' => $request->integer('actor_id') ?: null,
            'from' => trim((string) $request->input('from', '')),
            'until' => trim((string) $request->input('until', '')),
        ];
        $perPage = $request->integer('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = AuditEvent::query()->with('actor')->latest('created_at');
        if ($filters['q'] !== '') {
            $query->where(fn ($builder) => $builder
                ->where('action', 'ilike', '%'.$filters['q'].'%')
                ->orWhere('module', 'ilike', '%'.$filters['q'].'%')
                ->orWhere('ip_address', 'ilike', '%'.$filters['q'].'%')
                ->orWhere('route_name', 'ilike', '%'.$filters['q'].'%'));
        }
        if ($filters['module'] !== '') $query->where('module', $filters['module']);
        if ($filters['actor_id']) $query->where('actor_id', $filters['actor_id']);
        if ($filters['from'] !== '') $query->whereDate('created_at', '>=', $filters['from']);
        if ($filters['until'] !== '') $query->whereDate('created_at', '<=', $filters['until']);

        return view('modules.core.audit-log', [
            'events' => $query->paginate($perPage)->withQueryString(),
            'filters' => $filters,
            'perPage' => $perPage,
            'modules' => AuditEvent::query()->whereNotNull('module')->distinct()->orderBy('module')->pluck('module'),
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    private function authorizeAuditAccess(): void
    {
        abort_unless(auth()->user()?->hasRole('super_admin'), 403);
    }
}

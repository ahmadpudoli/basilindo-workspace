<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use App\Models\Document;
use App\Models\VerificationCase;
use App\Models\CrmAccount;
use App\Models\CrmOpportunity;
use App\Models\Ticket;
use App\Services\Documents\DocumentQueryService;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Schema;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    public function getTitle(): string
    {
        return 'Ringkasan Dokumen';
    }

    public function getViewData(): array
    {
        $user = auth()->user();
        $documents = app(DocumentQueryService::class)->visibleTo($user);
        $companyIds = $user->hasRole('super_admin')
            ? null
            : $user->companies()->select('core_companies.id');

        $stats = [
            'total' => (clone $documents)->count(),
            'ready' => (clone $documents)->where('status', 'ready')->count(),
            'pending' => (clone $documents)->whereIn('status', ['pending', 'quarantine'])->count(),
            'review' => VerificationCase::query()
                ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
                ->whereIn('status', ['open', 'in_review'])
                ->count(),
        ];

        $workspaceStats = [
            'accounts' => 0,
            'opportunities' => 0,
            'tickets' => 0,
            'pipeline' => [
                'qualification' => 0,
                'proposal' => 0,
                'negotiation' => 0,
                'won' => 0,
                'lost' => 0,
            ],
        ];

        if (Schema::connection('pgsql')->hasTable('crm_accounts')) {
            $workspaceStats['accounts'] = CrmAccount::query()
                ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
                ->count();
        }

        if (Schema::connection('pgsql')->hasTable('crm_opportunities')) {
            $opportunities = CrmOpportunity::query()
                ->when($companyIds, fn ($query) => $query->whereHas('account', fn ($accountQuery) => $accountQuery->whereIn('company_id', $companyIds)))
                ->get(['stage']);

            $workspaceStats['opportunities'] = $opportunities->whereNotIn('stage', ['won', 'lost'])->count();
            $workspaceStats['pipeline'] = array_replace(
                $workspaceStats['pipeline'],
                $opportunities->countBy('stage')->all(),
            );
        }

        if (Schema::connection('pgsql')->hasTable('tickets')) {
            $workspaceStats['tickets'] = Ticket::query()
                ->when($user->hasRole('super_admin'), fn ($query) => $query, fn ($query) => $query->whereHas('project.members', fn ($members) => $members->whereKey($user->getKey())))
                ->whereHas('status', fn ($status) => $status->where('is_completed', false))
                ->count();
        }

        $recentDocuments = (clone $documents)
            ->with(['company', 'documentType'])
            ->latest()
            ->limit(6)
            ->get();

        $activity = AuditEvent::query()
            ->with('actor')
            ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
            ->latest('created_at')
            ->limit(5)
            ->get();

        return compact('stats', 'workspaceStats', 'recentDocuments', 'activity');
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}

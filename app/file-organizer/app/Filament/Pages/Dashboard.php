<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use App\Models\Document;
use App\Models\VerificationCase;
use App\Services\Documents\DocumentQueryService;
use Filament\Pages\Dashboard as BaseDashboard;

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
            : $user->companies()->select('companies.id');

        $stats = [
            'total' => (clone $documents)->count(),
            'ready' => (clone $documents)->where('status', 'ready')->count(),
            'pending' => (clone $documents)->whereIn('status', ['pending', 'quarantine'])->count(),
            'review' => VerificationCase::query()
                ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
                ->whereIn('status', ['open', 'in_review'])
                ->count(),
        ];

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

        return compact('stats', 'recentDocuments', 'activity');
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

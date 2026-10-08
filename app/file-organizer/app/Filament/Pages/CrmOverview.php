<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CrmAccounts\CrmAccountResource;
use App\Filament\Resources\CrmActivityResource;
use App\Filament\Resources\CrmLeadResource;
use App\Filament\Resources\CrmOpportunityResource;
use Filament\Pages\Page;

class CrmOverview extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationLabel = 'CRM';
    protected static ?string $title = 'CRM';
    protected string $view = 'filament.pages.crm-overview';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'crm_admin', 'crm_member']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    protected function getViewData(): array
    {
        return [
            'companyCount' => auth()->user()?->companies()->count() ?? 0,
            'links' => [
                ['label' => 'Accounts', 'description' => 'Kelola customer dan account perusahaan.', 'url' => CrmAccountResource::getUrl()],
                ['label' => 'Leads', 'description' => 'Kelola prospek dan sumber lead.', 'url' => CrmLeadResource::getUrl()],
                ['label' => 'Opportunities', 'description' => 'Kelola pipeline dan konversi ke project.', 'url' => CrmOpportunityResource::getUrl()],
                ['label' => 'Activities', 'description' => 'Kelola follow-up, meeting, dan aktivitas CRM.', 'url' => CrmActivityResource::getUrl()],
            ],
        ];
    }
}

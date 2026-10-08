<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';
    protected string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return session('workspace.active_module') ? 'Dashboard Modul' : 'Basilindo Workspace';
    }

    public function getViewData(): array
    {
        $modules = [
            'modules' => [
                ['key' => 'core', 'label' => 'Core & Company', 'description' => 'Kelola pengguna, perusahaan, vendor, dan pengaturan workspace.', 'icon' => 'heroicon-o-cog-6-tooth', 'tone' => 'slate'],
                ['key' => 'crm', 'label' => 'CRM', 'description' => 'Kelola account, lead, opportunity, dan aktivitas pelanggan.', 'icon' => 'heroicon-o-chart-bar-square', 'tone' => 'cyan'],
                ['key' => 'project-management', 'label' => 'Project Management', 'description' => 'Kelola project, timeline, ticket, dan kolaborasi tim.', 'icon' => 'heroicon-o-rectangle-stack', 'tone' => 'violet'],
                ['key' => 'documents', 'label' => 'Document Management', 'description' => 'Simpan, verifikasi, cari, dan bundel dokumen perusahaan.', 'icon' => 'heroicon-o-document-text', 'tone' => 'emerald'],
            ],
        ];

        $activeKey = session('workspace.active_module');
        $activeModule = collect($modules['modules'])->firstWhere('key', $activeKey);

        return [
            ...$modules,
            'activeModule' => $activeModule,
            'activeLinks' => match ($activeKey) {
                'core' => [
                    ['label' => 'Users', 'description' => 'Kelola pengguna dan akses workspace.', 'url' => url('/users'), 'icon' => 'heroicon-o-users'],
                    ['label' => 'Perusahaan', 'description' => 'Kelola master perusahaan dan entitas bisnis.', 'url' => url('/companies'), 'icon' => 'heroicon-o-building-office-2'],
                    ['label' => 'Roles', 'description' => 'Kelola role dan permission aplikasi.', 'url' => url('/roles'), 'icon' => 'heroicon-o-shield-check'],
                ],
                'crm' => [
                    ['label' => 'CRM Overview', 'description' => 'Ringkasan aktivitas dan pipeline CRM.', 'url' => url('/crm-overview'), 'icon' => 'heroicon-o-chart-bar-square'],
                    ['label' => 'Accounts', 'description' => 'Kelola account pelanggan dan perusahaan.', 'url' => url('/crm-accounts'), 'icon' => 'heroicon-o-building-office'],
                    ['label' => 'Opportunities', 'description' => 'Kelola pipeline peluang bisnis.', 'url' => url('/crm-opportunities'), 'icon' => 'heroicon-o-banknotes'],
                ],
                'project-management' => [
                    ['label' => 'Projects', 'description' => 'Kelola project dan anggota tim.', 'url' => url('/projects'), 'icon' => 'heroicon-o-rectangle-stack'],
                    ['label' => 'Tickets', 'description' => 'Kelola pekerjaan dan ticket project.', 'url' => url('/tickets'), 'icon' => 'heroicon-o-ticket'],
                ],
                'documents' => [
                    ['label' => 'Dokumen', 'description' => 'Kelola arsip dan dokumen perusahaan.', 'url' => url('/documents'), 'icon' => 'heroicon-o-document-text'],
                    ['label' => 'Master Perusahaan', 'description' => 'Pilih perusahaan untuk katalog dokumen.', 'url' => url('/companies'), 'icon' => 'heroicon-o-building-office-2'],
                    ['label' => 'Jenis Dokumen', 'description' => 'Kelola klasifikasi dan metadata dokumen.', 'url' => url('/document-types'), 'icon' => 'heroicon-o-bookmark'],
                ],
                default => [],
            },
        ];
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

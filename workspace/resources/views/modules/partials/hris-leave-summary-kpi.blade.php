<section class="workspace-mobile-panel workspace-mobile-kpi-panel leave-summary-kpi-collapse mb-3" data-mobile-panel>
    <button type="button" class="workspace-mobile-panel-toggle" data-mobile-toggle aria-expanded="false" aria-controls="leave-summary-kpi-content">
        Ringkasan cuti & sakit <span data-mobile-toggle-icon>+</span>
    </button>
        <div id="leave-summary-kpi-content" class="workspace-overtime-summary-panel workspace-mobile-panel-content leave-summary-kpi-panel" aria-label="Ringkasan pengajuan cuti">
            <div class="workspace-overtime-summary-card leave-summary-kpi-heading">
                <span>Ikhtisar pengajuan · {{ $year ?? 'Semua tahun' }}</span>
                <strong>Aktivitas cuti & sakit</strong>
                <small>Ringkasan pengajuan pada scope perusahaan Anda.</small>
            </div>
            <div class="workspace-overtime-summary-card leave-summary-kpi-metric">
                <span>Total pengajuan</span>
                <strong>{{ number_format($metrics['total'], 0, ',', '.') }}</strong>
            </div>
            <div class="workspace-overtime-summary-card leave-summary-kpi-metric">
                <span>Masih diproses</span>
                <strong>{{ number_format($metrics['pending'], 0, ',', '.') }}</strong>
            </div>
            <div class="workspace-overtime-summary-card leave-summary-kpi-metric">
                <span>Hari disetujui</span>
                <strong>{{ number_format($metrics['approved_days'], 0, ',', '.') }}</strong>
            </div>
        </div>
</section>

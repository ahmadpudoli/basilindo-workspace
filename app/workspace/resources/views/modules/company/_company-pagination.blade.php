<div class="workspace-pagination-summary">Menampilkan <strong>{{ $companies->firstItem() ?? 0 }}</strong>–<strong>{{ $companies->lastItem() ?? 0 }}</strong> dari <strong>{{ $companies->total() }}</strong> perusahaan</div>
{{ $companies->onEachSide(1)->links() }}

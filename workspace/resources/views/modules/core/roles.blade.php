@extends('layouts.app', ['title' => 'Role'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="text-uppercase text-secondary small fw-semibold mb-2">Pengaturan akses</div><h1 class="h3 mb-1">Role dan permission</h1><p class="text-secondary mb-0">Kelola kelompok hak akses yang digunakan pengguna Workspace.</p></div>
    <a href="{{ route('workspace.core.roles.create') }}" class="btn btn-primary">Tambah role</a>
</div>

<form method="GET" class="card card-body border-0 shadow-sm mb-3 workspace-list-filter-form">
    <div class="row g-2 align-items-end">
        <div class="col-md-6 col-lg-5"><div class="workspace-floating-field"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama role"><label class="form-label">Pencarian</label></div></div>
        <div class="col-md-3 col-lg-2"><div class="workspace-floating-field"><select name="guard" class="form-select"><option value="">Semua guard</option><option value="web" @selected($guard === 'web')>web</option></select><label class="form-label">Guard</label></div></div>
        <div class="col-md-3 col-lg-2"><div class="workspace-floating-field"><select name="limit" class="form-select" aria-label="Jumlah baris per halaman"><option value="10" @selected($perPage === 10)>10 baris</option><option value="25" @selected($perPage === 25)>25 baris</option><option value="50" @selected($perPage === 50)>50 baris</option><option value="100" @selected($perPage === 100)>100 baris</option></select><label class="form-label">Tampilan</label></div></div>
        <div class="col-lg-3"><div class="workspace-filter-actions"><a href="{{ route('workspace.core.roles') }}" class="btn btn-sm btn-outline-secondary">Reset</a><button class="btn btn-sm btn-primary" type="submit">Terapkan</button></div></div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive"><table class="table align-middle mb-0">
        <thead><tr><th>Nama role</th><th>Pengguna</th><th>Guard</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        @forelse($roles as $role)
            <tr>
                <td><span class="fw-semibold">{{ $role->name }}</span>@if($role->name === 'super_admin') <span class="badge rounded-pill text-bg-primary ms-1">Sistem</span>@endif</td>
                <td>{{ $role->users_count }}</td><td><code class="workspace-code">{{ $role->guard_name }}</code></td>
                <td class="workspace-table-actions text-end text-nowrap">
                    <a href="{{ route('workspace.core.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary" title="Edit permission role">Edit</a>
                    @if($role->name !== 'super_admin')
                        <form method="POST" action="{{ route('workspace.core.roles.destroy', $role) }}" class="d-inline" data-confirm="Role {{ $role->name }} akan dihapus dan tidak dapat dipulihkan. Lanjutkan?" data-confirm-action="Hapus role"><span class="visually-hidden">Aksi hapus role</span>@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus role">Hapus</button></form>
                    @else
                        <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="Role sistem tidak dapat dihapus">Hapus</button><span class="visually-hidden">Role sistem tidak dapat dihapus.</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-secondary py-5">Belum ada role yang sesuai filter.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="card-footer bg-white workspace-list-pagination"><div class="workspace-pagination-summary">Menampilkan <strong>{{ $roles->firstItem() ?? 0 }}</strong>&ndash;<strong>{{ $roles->lastItem() ?? 0 }}</strong> dari <strong>{{ $roles->total() }}</strong> role</div>{{ $roles->onEachSide(1)->links() }}</div>
</div>
<p class="text-secondary small mt-3 mb-0">Aturan tombol: <strong>Edit</strong> untuk mengubah nama dan permission; <strong>Hapus</strong> hanya untuk role non-sistem dan selalu meminta konfirmasi. Role <code>super_admin</code> tidak dapat dihapus.</p>
@endsection

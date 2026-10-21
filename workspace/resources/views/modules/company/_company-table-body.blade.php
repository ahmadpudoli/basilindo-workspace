<tbody>
@forelse($companies as $company)
    <tr>
        <td>
            <div class="workspace-company-table-identity">
            @if($company->logo_object_key)
                <img src="{{ route('workspace.company.logo', $company) }}" alt="Logo {{ $company->name }}" class="workspace-company-table-logo" width="36" height="36" loading="lazy" decoding="async" fetchpriority="low">
            @else
                <span class="workspace-company-table-logo workspace-company-table-logo-placeholder" aria-label="Belum ada logo">{{ strtoupper(substr($company->code ?: $company->name, 0, 2)) }}</span>
            @endif
                <a href="{{ route('workspace.company.show', $company) }}" class="fw-semibold text-decoration-none">{{ $company->name }}</a>
            </div>
        </td>
        <td><code class="workspace-code">{{ $company->code }}</code></td>
        <td><span class="badge rounded-pill {{ $company->entity_type === 'group' ? 'text-bg-primary' : 'text-bg-info' }}">{{ $company->entity_type === 'group' ? 'Group' : 'Company' }}</span></td>
        <td><span class="badge rounded-pill {{ $company->classification === 'internal' ? 'text-bg-success' : 'text-bg-warning' }}">{{ $company->classification === 'internal' ? 'Internal' : 'External' }}</span></td>
        <td><span class="badge rounded-pill {{ $company->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $company->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td class="workspace-table-actions"><a class="btn btn-sm btn-outline-secondary" href="{{ route('workspace.company.edit', $company) }}">Edit</a><form method="POST" action="{{ route('workspace.company.destroy', $company) }}" class="d-inline" data-confirm="Perusahaan ini akan dipindahkan ke arsip. Lanjutkan?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Arsipkan</button></form></td>
    </tr>
@empty
    <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada perusahaan.</td></tr>
@endforelse
</tbody>

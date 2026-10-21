<tbody>
@forelse($companies as $company)
    <tr>
        <td><a href="{{ route('workspace.company.show', $company) }}" class="fw-semibold text-decoration-none">{{ $company->name }}</a></td>
        <td><code class="workspace-code">{{ $company->code }}</code></td>
        <td><span class="badge rounded-pill {{ $company->entity_type === 'group' ? 'text-bg-primary' : 'text-bg-info' }}">{{ $company->entity_type === 'group' ? 'Group' : 'Company' }}</span></td>
        <td><span class="badge rounded-pill {{ $company->classification === 'internal' ? 'text-bg-success' : 'text-bg-warning' }}">{{ $company->classification === 'internal' ? 'Internal' : 'External' }}</span></td>
        <td><span class="badge rounded-pill {{ $company->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $company->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td class="workspace-table-actions"><a class="btn btn-sm btn-outline-secondary" href="{{ route('workspace.company.edit', $company) }}">Edit</a></td>
    </tr>
@empty
    <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada perusahaan.</td></tr>
@endforelse
</tbody>

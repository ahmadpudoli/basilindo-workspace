<td class="workspace-table-actions text-end">
    @php($employeeHasRelations = $employee->leave_requests_count || $employee->subordinates_count)
    <button class="btn workspace-table-action-toggle" type="button" data-workspace-table-action-toggle data-menu-id="employee-action-menu-{{ $employee->getKey() }}" aria-controls="employee-action-menu-{{ $employee->getKey() }}" aria-haspopup="true" aria-expanded="false" aria-label="Aksi karyawan {{ $employee->full_name }}">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
    </button>
    <div id="employee-action-menu-{{ $employee->getKey() }}" class="dropdown-menu dropdown-menu-end workspace-table-action-menu" role="menu">
        @if(auth()->user()->hasRole('super_admin') || auth()->user()->canAny(['view_employee_payroll_account', 'update_employee_payroll_account', 'view_employee_reimbursement_account', 'update_employee_reimbursement_account']))
            <a class="dropdown-item" role="menuitem" href="{{ route('workspace.hris.employees.payment-accounts', $employee) }}">
                <span class="workspace-table-action-icon is-payment"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5.5" width="17" height="13" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M4 9h16M7 14h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                <span>Data pembayaran</span>
            </a>
        @endif
        <a class="dropdown-item" role="menuitem" href="{{ route('workspace.hris.employees.edit', $employee) }}">
            <span class="workspace-table-action-icon is-edit"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.2-.8L19 8.4 15.6 5 4.8 15.8 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span>Ubah</span>
        </a>
        <form method="POST" action="{{ route('workspace.hris.employees.destroy', $employee) }}" data-confirm="{{ $employeeHasRelations ? 'Karyawan ini memiliki relasi dan akan diarsipkan. Lanjutkan?' : 'Karyawan ini akan dihapus secara soft delete. Lanjutkan?' }}" data-confirm-action="{{ $employeeHasRelations ? 'Arsipkan' : 'Hapus' }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="dropdown-item {{ $employeeHasRelations ? '' : 'text-danger' }}" role="menuitem">
                <span class="workspace-table-action-icon is-danger"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-.8 13H6.8L6 7m4 4v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span>{{ $employeeHasRelations ? 'Arsipkan' : 'Hapus' }}</span>
            </button>
        </form>
    </div>
</td>

@extends('layouts.app', ['title' => 'Persetujuan HRIS'])

@section('content')
    <div class="mb-4">
        <div class="text-uppercase text-secondary small fw-semibold mb-2">Kehadiran &amp; claim</div>
        <h1 class="h3 mb-1">Persetujuan HRIS</h1>
        <p class="text-secondary mb-0">Tinjau pengajuan yang menunggu verifikasi manager/atasan atau HR.</p>
    </div>

    @foreach ([
        ['label' => 'Cuti & sakit', 'items' => $leaveApprovals, 'type' => 'leave', 'step' => 'manager'],
        ['label' => 'Lembur', 'items' => $overtimeApprovals, 'type' => 'overtime', 'step' => 'manager'],
        ['label' => 'Reimbursement & cash advance', 'items' => $claimApprovals, 'type' => 'claim', 'step' => 'manager'],
    ] as $section)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header">{{ $section['label'] }}</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Karyawan</th><th>Detail</th><th>Diajukan</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($section['items'] as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}</td>
                                <td>
                                    @if ($section['type'] === 'leave')
                                        {{ $item->type === 'leave' ? 'Cuti' : 'Sakit' }} · {{ $item->date_list }}
                                    @elseif ($section['type'] === 'overtime')
                                        {{ $item->work_date?->format('d/m/Y') }}
                                    @else
                                        {{ $item->type }}
                                    @endif
                                </td>
                                <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="workspace-table-actions text-end">
                                    <form method="POST" action="{{ route('workspace.hris.approve', [$section['type'], $item->getKey(), $section['step']]) }}" class="d-flex flex-column align-items-end gap-2">
                                        @csrf
                                        @if ($section['type'] === 'leave')
                                            <div class="d-flex gap-2"><select name="revision_type" class="form-select form-select-sm"><option value="substantive">Revisi substansi</option><option value="administrative">Revisi administrasi</option></select><input name="comment" class="form-control form-control-sm" placeholder="Alasan revisi"></div>
                                        @endif
                                        <div class="d-flex gap-2"><button name="action" value="reject" class="btn btn-sm btn-outline-danger">Tolak</button>@if ($section['type'] === 'leave')<button name="action" value="request_revision" class="btn btn-sm btn-outline-warning">Minta revisi</button>@endif<button name="action" value="approve" class="btn btn-sm btn-primary">Setujui</button></div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">Tidak ada pengajuan menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header">Cuti & sakit · Approval HR</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Karyawan</th><th>Detail</th><th>Diajukan</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($leaveHrApprovals as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->employee?->full_name ?? '-' }}</td>
                            <td>{{ $item->type === 'leave' ? 'Cuti' : 'Sakit' }} · {{ $item->date_list }}</td>
                            <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="workspace-table-actions text-end">
                                <form method="POST" action="{{ route('workspace.hris.approve', ['leave', $item->getKey(), 'hr']) }}" class="d-flex flex-column align-items-end gap-2">
                                    @csrf
                                    <div class="d-flex gap-2"><select name="revision_type" class="form-select form-select-sm"><option value="substantive">Revisi substansi</option><option value="administrative">Revisi administrasi</option></select><input name="comment" class="form-control form-control-sm" placeholder="Alasan revisi"></div>
                                    <div class="d-flex gap-2"><button name="action" value="reject" class="btn btn-sm btn-outline-danger">Tolak</button><button name="action" value="request_revision" class="btn btn-sm btn-outline-warning">Minta revisi</button><button name="action" value="approve" class="btn btn-sm btn-primary">Setujui</button></div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">Tidak ada pengajuan menunggu approval HR.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

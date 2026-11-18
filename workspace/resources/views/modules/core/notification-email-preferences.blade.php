@extends('layouts.app', ['title' => 'Preferensi notifikasi'])

@section('content')
<div class="container py-4" style="max-width: 760px">
    <div class="mb-4"><h1 class="h3 mb-1">Preferensi notifikasi</h1><p class="text-secondary mb-0">Atur notifikasi aplikasi yang juga dikirim ke alamat email akun Anda.</p></div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('workspace.notifications.preferences.update') }}" class="card border-0 shadow-sm">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="email-notifications" name="email_notifications_enabled" value="1" @checked($user->email_notifications_enabled)>
                <label class="form-check-label fw-semibold" for="email-notifications">Kirim notifikasi Workspace ke email</label>
            </div>
            <p class="small text-secondary mt-2 mb-0">Email dikirim ke {{ $user->email }}. Notifikasi di dalam Workspace tetap aktif saat opsi ini dimatikan.</p>
        </div>
        <div class="card-footer bg-white text-end"><button class="btn btn-primary">Simpan preferensi</button></div>
    </form>
</div>
@endsection

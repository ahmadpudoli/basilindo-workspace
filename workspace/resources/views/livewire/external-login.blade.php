<div class="row justify-content-center py-5">
    <div class="col-12 col-sm-9 col-md-6 col-lg-4">
        <div class="text-center mb-4">
            <img class="workspace-brand-logo mb-3" src="{{ asset('images/logo-basilindo.png') }}" alt="Logo Basilindo" width="38" height="38">
            <h1 class="h4 mb-1">{{ config('app.name') }}</h1>
            <p class="text-secondary mb-0">Akses portal eksternal</p>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h5 mb-1">Masukkan kode akses</h2>
                <p class="text-secondary small mb-4">Gunakan kode yang diberikan oleh admin Basilindo.</p>
                <form wire:submit="authenticate">
                    <div class="mb-3">
                        <label class="form-label" for="external-password">Kode akses</label>
                        <input class="form-control @error('password') is-invalid @enderror" type="password" id="external-password" wire:model="password" required autofocus>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if($error)
                        <div class="alert alert-danger py-2 small">{{ $error }}</div>
                    @endif
                    <button class="btn btn-primary w-100" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Masuk ke portal</span>
                        <span wire:loading>Memeriksa...</span>
                    </button>
                </form>
                <p class="text-center text-secondary small mt-4 mb-0">Powered by {{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('workspace.notifications.preferences.update') }}" class="account-preferences-form">
    @csrf @method('PUT')
    <div class="account-preferences-categories">
        <div class="account-preferences-intro"><strong>Atur email per kategori</strong><span>Email dikirim ke {{ $user->email }} sesuai kategori yang Anda aktifkan. Notifikasi dalam aplikasi tetap aktif.</span></div>
        @foreach($emailPreferenceCategories as $category => $details)
            <div class="account-preferences-category">
                <div><label for="email-category-{{ $category }}">{{ $details['label'] }}</label><small>{{ $details['description'] }}</small></div>
                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="email_notification_preferences[{{ $category }}]" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="email-category-{{ $category }}" name="email_notification_preferences[{{ $category }}]" value="1" @checked(old('email_notification_preferences.'.$category, $emailPreferences[$category] ?? true))>
                </div>
            </div>
        @endforeach
    </div>
    <div class="account-preferences-footer"><button class="btn btn-primary">Simpan preferensi</button></div>
</form>

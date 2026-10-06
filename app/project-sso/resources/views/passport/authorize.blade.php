<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Otorisasi {{ config('app.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f8fafc; font-family: Arial, sans-serif; color: #0f172a; }
        .card { width: min(440px, calc(100% - 32px)); padding: 32px; background: white; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 12px 30px rgba(15, 23, 42, .08); }
        h1 { margin: 0 0 12px; font-size: 24px; } p { color: #475569; line-height: 1.5; }
        .scopes { padding-left: 20px; color: #334155; } .actions { display: flex; gap: 12px; margin-top: 28px; }
        button { flex: 1; border: 0; border-radius: 8px; padding: 12px 16px; cursor: pointer; font-weight: 600; }
        .approve { background: #2563eb; color: white; } .deny { background: #e2e8f0; color: #334155; }
    </style>
</head>
<body>
<main class="card">
    <h1>Berikan akses aplikasi?</h1>
    <p><strong>{{ $client->name }}</strong> meminta akses ke akun SSO Anda.</p>
    @if (count($scopes))
        <p>Aplikasi akan memperoleh:</p>
        <ul class="scopes">
            @foreach ($scopes as $scope)
                <li>{{ $scope->description }}</li>
            @endforeach
        </ul>
    @endif
    <div class="actions">
        <form method="POST" action="{{ route('passport.authorizations.deny') }}" style="flex:1">
            @csrf
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <button class="deny" type="submit">Tolak</button>
        </form>
        <form method="POST" action="{{ route('passport.authorizations.approve') }}" style="flex:1">
            @csrf
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <button class="approve" type="submit">Izinkan</button>
        </form>
    </div>
</main>
</body>
</html>

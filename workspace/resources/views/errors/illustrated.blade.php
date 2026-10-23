<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} · Basilindo Workspace</title>
    <style>
        :root{color-scheme:light;--blue:#0b74c9;--blue-dark:#075a9d;--ink:#101828;--muted:#667085;--line:#dbe7f1;--bg:#f6f9fc}
        *{box-sizing:border-box}body{margin:0;min-width:320px;background:radial-gradient(circle at 12% 8%,#e7f5ff 0,transparent 32%),var(--bg);color:var(--ink);font:15px/1.5 Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
        .error-page{align-items:center;display:flex;justify-content:center;min-height:100vh;overflow:hidden;padding:28px;position:relative}.error-page:before{background-image:radial-gradient(#b9d9ef 1px,transparent 1px);background-size:22px 22px;content:"";inset:0;opacity:.35;pointer-events:none;position:absolute}
        .error-orbit{border:1px solid #c8e3f5;border-radius:50%;height:390px;position:absolute;right:-150px;top:-170px;width:390px}.error-orbit:after{border:1px solid #d8ebf8;border-radius:50%;content:"";inset:52px;position:absolute}.error-card{align-items:center;background:rgba(255,255,255,.9);border:1px solid rgba(219,231,241,.95);border-radius:24px;box-shadow:0 20px 60px rgba(22,71,106,.12);display:grid;gap:clamp(28px,6vw,82px);grid-template-columns:minmax(170px,230px) minmax(0,430px);max-width:820px;padding:clamp(28px,5vw,62px);position:relative;width:100%;z-index:1}
        .error-illustration{align-items:center;display:flex;height:220px;justify-content:center;position:relative}.error-blob{animation:error-float 4s ease-in-out infinite;background:linear-gradient(145deg,#e5f5ff,#b9e1fa);border-radius:45% 55% 58% 42%;height:176px;position:relative;transform:rotate(-8deg);width:176px}.error-blob:before{background:#fff;border:1px solid #c8e3f5;border-radius:16px;box-shadow:0 10px 20px rgba(11,116,201,.12);content:"";height:82px;left:50%;position:absolute;top:43px;transform:translateX(-50%) rotate(8deg);width:66px}.error-blob:after{background:var(--blue);border-radius:50%;box-shadow:24px 0 0 var(--blue);content:"";height:7px;left:65px;position:absolute;top:78px;width:7px}.error-symbol{align-items:center;color:var(--blue);display:flex;height:58px;justify-content:center;left:50%;position:absolute;top:55px;transform:translateX(-50%) rotate(8deg);width:58px;z-index:1}.error-symbol svg{fill:none;height:42px;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.7;width:42px}.error-code{animation:error-pulse 3s ease-in-out infinite;color:var(--blue);font-size:1.4rem;font-weight:800;left:50%;letter-spacing:.08em;position:absolute;top:8px;transform:translateX(-50%);z-index:2}.error-copy{animation:error-rise .55s ease both}.error-eyebrow{color:var(--blue);font-size:.72rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.error-copy h1{font-size:clamp(1.8rem,4vw,2.7rem);letter-spacing:-.05em;line-height:1.05;margin:10px 0 12px}.error-copy p{color:var(--muted);margin:0 0 24px;max-width:410px}.error-actions{display:flex;flex-wrap:wrap;gap:10px}.error-button{border:1px solid var(--blue);border-radius:9px;cursor:pointer;display:inline-flex;font:600 .86rem inherit;justify-content:center;padding:10px 16px;text-decoration:none;transition:transform .18s ease,box-shadow .18s ease}.error-button:hover{box-shadow:0 8px 18px rgba(11,116,201,.18);transform:translateY(-2px)}.error-button-primary{background:var(--blue);color:#fff}.error-button-secondary{background:#fff;color:var(--blue);border-color:var(--line)}.error-brand{bottom:20px;color:#8295a7;font-size:.72rem;left:50%;position:absolute;transform:translateX(-50%)}
        @keyframes error-float{0%,100%{transform:translateY(0) rotate(-8deg)}50%{transform:translateY(-10px) rotate(-4deg)}}@keyframes error-pulse{0%,100%{opacity:.8;transform:translateX(-50%) scale(1)}50%{opacity:1;transform:translateX(-50%) scale(1.08)}}@keyframes error-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
        @media(max-width:680px){.error-page{padding:18px}.error-card{display:block;padding:34px 24px;text-align:center}.error-illustration{height:180px}.error-blob{height:140px;width:140px}.error-blob:before{top:33px}.error-blob:after{left:51px;top:62px}.error-code{top:2px}.error-copy p{margin-left:auto;margin-right:auto}.error-actions{justify-content:center}.error-brand{bottom:10px}}
        @media(prefers-reduced-motion:reduce){.error-blob,.error-code,.error-copy{animation:none}.error-button{transition:none}}
    </style>
</head>
<body>
    <main class="error-page">
        <div class="error-orbit" aria-hidden="true"></div>
        <section class="error-card" aria-labelledby="error-title">
            <div class="error-illustration" aria-hidden="true"><div class="error-blob"><span class="error-symbol">
                @if($code === 401 || $code === 403)<svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>
                @elseif($code === 404)<svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="5.5"/><path d="m15 15 4 4M8.5 10.5h4"/></svg>
                @elseif($code === 408 || $code === 419 || $code === 504)<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg>
                @elseif($code === 422)<svg viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="16" rx="2"/><path d="m8 9 1.5 1.5L12 8M13.5 9H16M8 14l1.5 1.5L12 13M13.5 14H16"/></svg>
                @elseif($code === 429)<svg viewBox="0 0 24 24"><path d="M5 6h14M5 12h10M5 18h6"/><path d="m17 14 3 3-3 3M20 17h-7"/></svg>
                @elseif($code === 500 || $code === 503)<svg viewBox="0 0 24 24"><path d="m14 6 4 4M6 18l7-7M15 5a3 3 0 0 0 4 4l-3 3-4-4 3-3ZM5 19l2-2"/><path d="M16 14 20 18"/></svg>
                @elseif($code === 502)<svg viewBox="0 0 24 24"><path d="M7 5v14M17 5v14M7 9h10M7 15h10"/><path d="m10 12 2-2 2 2-2 2-2-2Z"/></svg>
                @else<svg viewBox="0 0 24 24"><path d="M12 4v9M12 17v1M5 20h14a1 1 0 0 0 .9-1.45L13 5a1.15 1.15 0 0 0-2 0l-6.9 13.55A1 1 0 0 0 5 20Z"/></svg>@endif
            </span><span class="error-code">{{ $code }}</span></div></div>
            <div class="error-copy"><span class="error-eyebrow">Basilindo Workspace</span><h1 id="error-title">{{ $title }}</h1><p>{{ $message }}</p><div class="error-actions"><a class="error-button error-button-primary" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Ke dashboard' : 'Ke halaman login' }}</a><button class="error-button error-button-secondary" type="button" onclick="history.back()">Kembali</button></div></div>
        </section>
        <div class="error-brand">Basilindo Workspace · Sistem kerja terpadu</div>
    </main>
</body>
</html>

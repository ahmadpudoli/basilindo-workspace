<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $notification->title }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f5f7;font-family:Arial,Helvetica,sans-serif;color:#243247;-webkit-text-size-adjust:100%;">
    <div style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;mso-hide:all;">
        {{ $notification->title }} — pemberitahuan dari Basilindo Workspace.
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f3f5f7;border-collapse:collapse;width:100%;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:620px;border-collapse:separate;border-spacing:0;background:#ffffff;border:1px solid #e2e8ef;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="height:5px;background:#1769a8;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:22px 30px 19px;border-bottom:1px solid #e9edf2;">
                            <div style="font-size:12px;line-height:16px;font-weight:700;letter-spacing:1.1px;color:#31516f;">BASILINDO <span style="font-weight:400;color:#718195;">WORKSPACE</span></div>
                            <div style="margin-top:5px;font-size:12px;line-height:18px;color:#8290a0;">Pemberitahuan aktivitas akun</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <div style="margin-bottom:12px;font-size:11px;line-height:16px;font-weight:700;letter-spacing:.9px;color:#647b91;text-transform:uppercase;">Pembaruan Workspace</div>
                            <h1 style="margin:0 0 16px;font-size:22px;line-height:1.4;font-weight:700;color:#1d3044;">{{ $notification->title }}</h1>
                            <div style="height:1px;background:#e9edf2;font-size:0;line-height:0;">&nbsp;</div>
                            <p style="margin:19px 0 0;font-size:15px;line-height:1.75;color:#425466;">{{ $notification->message }}</p>
                            @if($targetUrl)
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-top:25px;border-collapse:separate;">
                                    <tr>
                                        <td align="center" bgcolor="#1769a8" style="border-radius:6px;">
                                            <a href="{{ $targetUrl }}" style="display:inline-block;padding:12px 18px;border:1px solid #1769a8;border-radius:6px;color:#ffffff;font-size:14px;line-height:20px;font-weight:700;text-decoration:none;">{{ $targetLabel }}</a>
                                        </td>
                                    </tr>
                                </table>
                                <div style="margin-top:21px;padding:12px 14px;background:#f7f9fb;border:1px solid #e8edf2;border-radius:6px;">
                                    <div style="margin-bottom:5px;font-size:11px;line-height:15px;font-weight:700;color:#62758a;">Tautan langsung</div>
                                    <a href="{{ $targetUrl }}" style="font-size:12px;line-height:18px;color:#356d9e;text-decoration:underline;word-break:break-all;">{{ $targetUrl }}</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 30px;background:#f8fafc;border-top:1px solid #e9edf2;">
                            <p style="margin:0;font-size:12px;line-height:19px;color:#6e7d8e;">Email ini dikirim ke <strong style="color:#4e6072;">{{ $recipient->email }}</strong> sesuai preferensi notifikasi akun Anda.</p>
                            <p style="margin:7px 0 0;font-size:11px;line-height:17px;color:#8995a2;">Pesan ini dikirim otomatis oleh Basilindo Workspace. Mohon tidak membalas email ini.</p>
                        </td>
                    </tr>
                </table>
                <div style="padding:16px 8px 0;font-size:11px;line-height:17px;color:#8995a2;text-align:center;">Basilindo Workspace · Notifikasi sistem</div>
            </td>
        </tr>
    </table>
</body>
</html>

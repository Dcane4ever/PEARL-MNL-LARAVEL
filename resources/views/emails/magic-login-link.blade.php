<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login Link</title>
</head>
<body style="margin:0;padding:0;background:#eef4fb;font-family:Poppins,Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef4fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
                    <tr>
                        <td style="padding:18px 24px;background:#0f172a;color:#e2e8f0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:74px;vertical-align:middle;">
                                        <img src="{{ asset('image/email-logo-placeholder.png') }}" alt="The Pearl Manila Logo" style="width:60px;height:60px;object-fit:contain;display:block;">
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <div style="font-size:20px;font-weight:700;line-height:1.2;">The Pearl Manila</div>
                                        <div style="font-size:13px;color:#cbd5e1;">Secure Customer Login</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 24px;">
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">Hi {{ $user->name }},</p>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;">
                                Click the button below to sign in to your account. This one-time link expires at
                                <strong>{{ $expiresAt->format('M d, Y h:i A') }}</strong>.
                            </p>

                            <p style="margin:0 0 20px;text-align:center;">
                                <a href="{{ $magicLink }}" style="display:inline-block;background:#0ea5e9;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:700;">
                                    Log In Securely
                                </a>
                            </p>

                            <p style="margin:0 0 10px;font-size:14px;line-height:1.7;color:#475569;">
                                If you did not request this link, you can ignore this email.
                            </p>
                            <p style="margin:0;font-size:13px;color:#64748b;word-break:break-all;">
                                Direct link: {{ $magicLink }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

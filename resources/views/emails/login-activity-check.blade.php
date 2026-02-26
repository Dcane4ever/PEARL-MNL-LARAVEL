<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Activity Verification</title>
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
                                        <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="The Pearl Manila Logo" style="width:60px;height:60px;object-fit:contain;display:block;">
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <div style="font-size:20px;font-weight:700;line-height:1.2;">The Pearl Manila</div>
                                        <div style="font-size:13px;color:#cbd5e1;">Login Verification</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 24px;">
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">Hi {{ $user->name }},</p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;">
                                Did you log in on this device?
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 14px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:600;">Time</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ $loggedInAt->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:600;">IP Address</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ $ipAddress }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;font-weight:600;">Device</td>
                                    <td style="padding:10px 12px;">{{ \Illuminate\Support\Str::limit($userAgent, 140) }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px;text-align:center;">
                                <a href="{{ $yesUrl }}" style="display:inline-block;background:#16a34a;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:700;margin-right:8px;">
                                    Yes, this was me
                                </a>
                                <a href="{{ $noUrl }}" style="display:inline-block;background:#dc2626;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-weight:700;">
                                    No, secure my account
                                </a>
                            </p>

                            <p style="margin:0;font-size:13px;line-height:1.6;color:#64748b;">
                                If this was not you, choose "No" immediately to sign out active sessions.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

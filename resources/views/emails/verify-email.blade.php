<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email - The Pearl Manila</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family: Arial, sans-serif; color:#1f2937;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f7fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="background:#ffffff; border-radius:12px; padding:32px; box-shadow:0 10px 30px rgba(15,23,42,0.08);">
                    <tr>
                        <td style="text-align:center; padding-bottom:16px;">
                            <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="The Pearl Manila" style="max-width:160px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <h1 style="margin:0 0 12px; font-size:22px; color:#111827;">Verify Your Email</h1>
                            <p style="margin:0 0 24px; font-size:14px; color:#4b5563;">
                                Hi {{ $user->name ?? 'Guest' }}, please confirm your email address to continue.
                            </p>
                            <a href="{{ $verificationUrl }}" style="display:inline-block; padding:12px 24px; background:#2563eb; color:#ffffff; text-decoration:none; border-radius:8px; font-weight:600;">
                                Verify Email Address
                            </a>
                            <p style="margin:24px 0 0; font-size:12px; color:#6b7280;">
                                If you did not create an account, no further action is required.
                            </p>
                            <p style="margin:12px 0 0; font-size:12px; color:#9ca3af;">
                                The Pearl Manila Hotel
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Update</title>
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
                                        <div style="font-size:13px;color:#cbd5e1;">Booking Update</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 24px;">
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">Hi {{ $booking->user->name }},</p>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;">{{ $messageLine }}</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                                <tr>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Booking ID</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">#{{ $booking->id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Room</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ $booking->room->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Check-in</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }} {{ $booking->check_in_time }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Check-out</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }} {{ $booking->check_out_time }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Guests</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ $booking->adults }} adult(s), {{ $booking->children }} child(ren)</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;background:#f8fafc;font-weight:600;">Rooms</td>
                                    <td style="padding:10px 12px;border-bottom:1px solid #e2e8f0;">{{ $booking->rooms_count }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 12px;background:#f8fafc;font-weight:600;">Status</td>
                                    <td style="padding:10px 12px;">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</td>
                                </tr>
                            </table>

                            @if(!empty($booking->verification_notes))
                                <p style="margin:16px 0 0;font-size:14px;line-height:1.6;"><strong>Admin Notes:</strong> {{ $booking->verification_notes }}</p>
                            @endif

                            <p style="margin:18px 0 0;font-size:14px;color:#475569;">If you have questions, please contact The Pearl Manila front desk.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

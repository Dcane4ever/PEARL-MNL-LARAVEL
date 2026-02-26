<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pearl-user-id" content="{{ auth()->id() }}">
    <meta name="pearl-user-admin" content="{{ auth()->check() && auth()->user()->is_admin ? '1' : '0' }}">
    <title>Online Payment - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
    @vite(['resources/js/app.js'])
    <style>
        .payment-page-wrap {
            max-width: 980px;
            margin: 0 auto;
            padding: 26px 20px 40px;
        }
        .payment-card {
            background: #ffffff;
            border: 1px solid #dbe5f2;
            border-radius: 16px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            padding: 22px;
        }
        .payment-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 18px;
            margin-top: 16px;
        }
        .payment-panel {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            background: #f8fafc;
        }
        .payment-panel h3 {
            margin: 0 0 8px;
            font-size: 1rem;
            color: #0f172a;
        }
        .payment-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 700;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .payment-status-pill.is-submitted {
            background: #fef3c7;
            color: #92400e;
            border-color: #fcd34d;
        }
        .payment-status-pill.is-verified {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }
        .payment-status-pill.is-rejected {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }
        .payment-qr {
            display: grid;
            place-items: center;
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 16px;
        }
        .payment-qr img {
            max-width: 220px;
            width: 100%;
            height: auto;
        }
        .payment-form input[type="file"] {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px;
            background: #fff;
            font-size: 0.9rem;
        }
        .payment-form input[type="text"],
        .payment-form input[type="number"] {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 9px 10px;
            background: #fff;
            font-size: 0.9rem;
        }
        .payment-form .input-hint {
            margin-top: 6px;
            font-size: 0.78rem;
            color: #64748b;
        }
        .payment-actions {
            margin-top: 12px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .payment-alert {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #0f172a;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
        }
        .payment-alert.is-error {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .payment-alert.is-warning {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }
        html.dark-theme .payment-card {
            background: #0f172a;
            border-color: #334155;
            box-shadow: 0 20px 36px rgba(2, 6, 23, 0.6);
        }
        html.dark-theme .payment-panel {
            background: #0b1220;
            border-color: #334155;
        }
        html.dark-theme .payment-panel h3,
        html.dark-theme .payment-card h2,
        html.dark-theme .payment-card p,
        html.dark-theme .payment-card li {
            color: #e2e8f0;
        }
        html.dark-theme .payment-qr {
            background: #0f172a;
            border-color: #475569;
        }
        html.dark-theme .payment-form input[type="file"] {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        html.dark-theme .payment-form input[type="text"],
        html.dark-theme .payment-form input[type="number"] {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        html.dark-theme .payment-form .input-hint {
            color: #94a3b8;
        }
        @media (max-width: 880px) {
            .payment-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar reveal-on-scroll">
        <div class="container nav-container">
            <div class="logo">
                <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="Pearl Manila">
                The Pearl Manila
            </div>
            <ul class="nav-menu">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/rooms') }}">Rooms</a></li>
                <li><a href="{{ url('/facilities') }}">Facilities</a></li>
                <li><a href="{{ route('rooms.booking') }}">Check In</a></li>
                <li><a href="{{ route('rooms.history') }}">Booking History</a></li>
                <li>
                    <button class="theme-toggle" type="button" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                        <span class="theme-toggle-label">Dark</span>
                    </button>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <section class="auth-page">
        <div class="payment-page-wrap">
            <div class="payment-card reveal-on-scroll">
                <h2>Pay Now Online</h2>
                <p>Use the AUB QR code below, then upload your payment screenshot for manual verification.</p>

                @if (session('payment_status'))
                    <div class="payment-alert">{{ session('payment_status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="payment-alert is-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="payment-grid">
                    <div class="payment-panel">
                        <h3>Booking Details</h3>
                        @php
                            $checkIn = $booking->check_in_date ? \Carbon\Carbon::parse($booking->check_in_date) : null;
                            $checkOut = $booking->check_out_date ? \Carbon\Carbon::parse($booking->check_out_date) : null;
                            $stayNights = 1;
                            if ($checkIn && $checkOut) {
                                $stayNights = max(1, $checkIn->diffInDays($checkOut));
                            }
                            $baseRate = (float) ($booking->room?->base_rate ?? 0);
                            $roomsCount = (int) ($booking->rooms_count ?? 1);
                            $totalAmount = $baseRate * $stayNights * $roomsCount;
                            $downpaymentAmount = $totalAmount * 0.15;
                        @endphp
                        <p><strong>Reference:</strong> #{{ $booking->id }}</p>
                        <p><strong>Room:</strong> {{ $booking->room?->name ?? 'Room' }}</p>
                        <p><strong>Check-in:</strong> {{ $checkIn ? $checkIn->format('M d, Y') : '-' }} {{ $booking->check_in_time }}</p>
                        <p><strong>Check-out:</strong> {{ $checkOut ? $checkOut->format('M d, Y') : '-' }} {{ $booking->check_out_time }}</p>
                        <p><strong>Guests:</strong> {{ $booking->adults }} adults, {{ $booking->children }} children</p>
                        <p><strong>Stay nights:</strong> {{ $stayNights }}</p>
                        <p><strong>Rooms:</strong> {{ $roomsCount }} room(s)</p>
                        <p><strong>Rate per night:</strong> PHP {{ number_format($baseRate, 2) }}</p>
                        <p><strong>Total amount:</strong> PHP {{ number_format($totalAmount, 2) }}</p>
                        <p style="font-size:0.82rem; color:#64748b; margin-top:6px;">
                            Minimum downpayment (15%): PHP {{ number_format($downpaymentAmount, 2) }}
                        </p>
                        @if (!empty($booking->payment_amount))
                            <p><strong>Submitted amount:</strong> PHP {{ number_format((float) $booking->payment_amount, 2) }}</p>
                        @endif
                        @if (!empty($booking->payment_reference))
                            <p><strong>Reference no:</strong> {{ $booking->payment_reference }}</p>
                        @endif

                        @php
                            $paymentStatus = $booking->payment_status ?? 'unpaid';
                            $paymentAttempts = (int) ($booking->payment_attempts ?? 0);
                            $remainingAttempts = max(0, 3 - $paymentAttempts);
                        @endphp
                        <div style="margin-top: 10px;">
                            <span class="payment-status-pill {{ $paymentStatus === 'submitted' ? 'is-submitted' : '' }} {{ $paymentStatus === 'verified' ? 'is-verified' : '' }} {{ $paymentStatus === 'rejected' ? 'is-rejected' : '' }}">
                                {{ $paymentStatus === 'submitted' ? 'Payment to be Confirmed' : ($paymentStatus === 'pay_on_site' ? 'Pay on Site' : ucfirst($paymentStatus)) }}
                            </span>
                            @if (in_array($paymentStatus, ['unpaid', 'rejected'], true))
                                <div style="margin-top: 6px; font-size: 0.78rem; color: #64748b;">
                                    Attempts remaining: {{ $remainingAttempts }} / 3
                                </div>
                            @endif
                        </div>

                        @if ($paymentStatus === 'rejected' && $booking->payment_notes)
                            <div class="payment-alert is-warning" style="margin-top: 10px;">
                                {{ $booking->payment_notes }}
                            </div>
                        @endif
                    </div>

                    <div class="payment-panel">
                        <h3>AUB QR Code</h3>
                        <div class="payment-qr">
                            <img src="{{ asset('image/qrcode.png') }}" alt="AUB QR Code">
                        </div>
                        <p style="margin-top: 10px; font-size: 0.85rem; color: #64748b;">
                            Scan this AUB QR to make payments or downpayment of 15%.
                        </p>

                        <div class="payment-form" style="margin-top: 12px;">
                            @if (in_array($paymentStatus, ['unpaid', 'rejected'], true) && $remainingAttempts > 0)
                                <form method="POST" action="{{ route('rooms.payment.submit', $booking) }}" enctype="multipart/form-data">
                                    @csrf
                                    <label for="payment_amount"><strong>Amount paid (PHP)</strong></label>
                                    <input
                                        id="payment_amount"
                                        name="payment_amount"
                                        type="number"
                                        inputmode="decimal"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('payment_amount') }}"
                                        data-downpayment="{{ number_format($downpaymentAmount, 2, '.', '') }}"
                                        data-total="{{ number_format($totalAmount, 2, '.', '') }}"
                                        required
                                    >
                                    <div class="input-hint">
                                        Accepted amounts: PHP {{ number_format($downpaymentAmount, 2) }} (downpayment) or PHP {{ number_format($totalAmount, 2) }} (full).
                                    </div>

                                    <label for="payment_reference" style="margin-top: 10px;"><strong>Reference number</strong></label>
                                    <input
                                        id="payment_reference"
                                        name="payment_reference"
                                        type="text"
                                        maxlength="100"
                                        value="{{ old('payment_reference') }}"
                                        required
                                    >

                                    <label for="payment_proof" style="margin-top: 10px;"><strong>Upload payment screenshot (max 5MB)</strong></label>
                                    <input id="payment_proof" name="payment_proof" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="payment-actions">
                                        <button class="btn btn-primary" type="submit">Submit Proof</button>
                                    </div>
                                </form>
                            @elseif ($paymentStatus === 'submitted')
                                <div class="payment-alert is-warning">Payment submitted. Please wait for admin confirmation.</div>
                            @elseif ($paymentStatus === 'pay_on_site' || $remainingAttempts === 0)
                                <div class="payment-alert is-error">
                                    Online payment is no longer available. Please proceed to the front desk to settle payment.
                                </div>
                            @elseif ($paymentStatus === 'verified')
                                <div class="payment-alert">Payment accepted. Please proceed to your booking details.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/test.js') }}"></script>
    <script>
        window.PearlLiveRefresh = (payload = {}) => {
            if (payload?.scope === 'poll') {
                return;
            }
            window.location.reload();
        };

        window.dispatchEvent(new Event('pearl:live-ready'));

        const paymentAmountInput = document.getElementById('payment_amount');
        if (paymentAmountInput) {
            const downpayment = parseFloat(paymentAmountInput.dataset.downpayment || '0');
            const total = parseFloat(paymentAmountInput.dataset.total || '0');
            const message = `Amount must be PHP ${downpayment.toFixed(2)} (downpayment) or PHP ${total.toFixed(2)} (full).`;

            const validateAmount = () => {
                if (!paymentAmountInput.value) {
                    paymentAmountInput.setCustomValidity('');
                    return;
                }
                const value = parseFloat(paymentAmountInput.value);
                const matchesDown = Math.abs(value - downpayment) < 0.01;
                const matchesTotal = Math.abs(value - total) < 0.01;
                paymentAmountInput.setCustomValidity(matchesDown || matchesTotal ? '' : message);
            };

            paymentAmountInput.addEventListener('input', validateAmount);
            paymentAmountInput.addEventListener('blur', validateAmount);
            validateAmount();
        }
    </script>
</body>
</html>


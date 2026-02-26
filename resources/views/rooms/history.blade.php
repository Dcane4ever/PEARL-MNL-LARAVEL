<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pearl-user-id" content="{{ auth()->id() }}">
    <meta name="pearl-user-admin" content="{{ auth()->check() && auth()->user()->is_admin ? '1' : '0' }}">
    <title>Booking History - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
    @vite(['resources/js/app.js'])
    <style>
        .history-page-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }
        .history-card {
            background: #ffffff;
            border: 1px solid #dbe5f2;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            padding: 20px;
        }
        .history-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-end;
            margin-bottom: 16px;
        }
        .history-head h2 {
            margin: 0;
            color: #0f172a;
        }
        .history-head p {
            margin: 4px 0 0;
            color: #64748b;
        }
        .history-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }
        .history-filter-grid .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .history-filter-grid label {
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .history-filter-grid input,
        .history-filter-grid select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 0.9rem;
            background: #fff;
            color: #0f172a;
        }
        .history-filter-actions {
            margin-top: 6px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .history-table-wrap {
            overflow-x: auto;
            border: 1px solid #dbe5f2;
            border-radius: 12px;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 920px;
        }
        .history-table th,
        .history-table td {
            padding: 11px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 0.88rem;
            color: #1f2937;
        }
        .history-table th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #475569;
            background: #f8fafc;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .history-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 9px;
            border: 1px solid transparent;
        }
        .history-status.status-pending,
        .history-status.status-pending_verification {
            background: #fef3c7;
            color: #92400e;
            border-color: #fcd34d;
        }
        .history-status.status-confirmed {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }
        .history-status.status-checked_in {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #93c5fd;
        }
        .history-status.status-checkout_scheduled {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #93c5fd;
        }
        .history-status.status-checked_out {
            background: #e2e8f0;
            color: #334155;
            border-color: #cbd5e1;
        }
        .history-status.status-cancelled {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }
        .payment-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 8px;
            border: 1px solid transparent;
        }
        .payment-status.status-unpaid {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }
        .payment-status.status-submitted {
            background: #fef3c7;
            color: #92400e;
            border-color: #fcd34d;
        }
        .payment-status.status-verified {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }
        .payment-status.status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }
        .history-empty {
            padding: 22px;
            text-align: center;
            color: #64748b;
        }
        .history-pagination {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            color: #64748b;
            font-size: 0.84rem;
        }
        .history-pagination-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .history-pagination-links a,
        .history-pagination-links span {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 10px;
            text-decoration: none;
            color: #0f172a;
            background: #fff;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .history-pagination-links span.is-disabled {
            opacity: .45;
            cursor: not-allowed;
        }
        html.dark-theme .history-card {
            background: #0f172a;
            border-color: #334155;
            box-shadow: 0 16px 30px rgba(2, 6, 23, 0.55);
        }
        html.dark-theme .history-head h2 {
            color: #f8fafc;
        }
        html.dark-theme .history-head p {
            color: #94a3b8;
        }
        html.dark-theme .history-filter-grid label {
            color: #cbd5e1;
        }
        html.dark-theme .history-filter-grid input,
        html.dark-theme .history-filter-grid select {
            background: #0b1220;
            border-color: #334155;
            color: #e5e7eb;
        }
        html.dark-theme .history-table-wrap {
            border-color: #334155;
        }
        html.dark-theme .history-table th {
            background: #1e293b;
            border-bottom-color: #334155;
            color: #e2e8f0;
        }
        html.dark-theme .history-table td {
            border-bottom-color: #334155;
            color: #e5e7eb;
        }
        html.dark-theme .history-pagination {
            color: #94a3b8;
        }
        html.dark-theme .history-pagination-links a,
        html.dark-theme .history-pagination-links span {
            background: #0b1220;
            border-color: #334155;
            color: #e2e8f0;
        }
        @media (max-width: 960px) {
            .history-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .history-filter-grid {
                grid-template-columns: 1fr;
            }
            .history-filter-actions {
                justify-content: flex-start;
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
        <div class="history-page-wrap">
            <div class="history-card reveal-on-scroll">
                <div class="history-head">
                    <div>
                        <h2>My Booking History</h2>
                        <p>Review all your bookings and latest statuses.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('rooms.history') }}">
                    <div class="history-filter-grid">
                        <div class="filter-group">
                            <label for="reference">Reference #</label>
                            <input id="reference" name="reference" value="{{ $referenceFilter }}" placeholder="e.g. 1024">
                        </div>
                        <div class="filter-group">
                            <label for="status_filter">Status</label>
                            <select id="status_filter" name="status_filter">
                                <option value="">All statuses</option>
                                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="pending_verification" {{ $statusFilter === 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                                <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="checked_in" {{ $statusFilter === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                <option value="checkout_scheduled" {{ $statusFilter === 'checkout_scheduled' ? 'selected' : '' }}>Checkout Scheduled</option>
                                <option value="checked_out" {{ $statusFilter === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="check_in_from">Check-in From</label>
                            <input id="check_in_from" name="check_in_from" type="date" value="{{ $checkInFrom }}">
                        </div>
                        <div class="filter-group">
                            <label for="check_out_to">Check-out To</label>
                            <input id="check_out_to" name="check_out_to" type="date" value="{{ $checkOutTo }}">
                        </div>
                    </div>
                    <div class="history-filter-actions">
                        <a href="{{ route('rooms.history') }}" class="btn btn-outline">Reset</a>
                        <button class="btn btn-primary" type="submit">Apply Filters</button>
                    </div>
                </form>

                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Room</th>
                                <th>Stay Dates</th>
                                <th>Guests</th>
                                <th>Rooms</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Booked At</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings as $booking)
                                @php
                                    $paymentStatus = $booking->payment_status ?? 'unpaid';
                                    $paymentLabel = $paymentStatus === 'submitted'
                                        ? 'Payment to be Confirmed'
                                        : ($paymentStatus === 'pay_on_site' ? 'Pay on Site' : ucfirst($paymentStatus));
                                @endphp
                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->room?->name ?? 'Room removed' }}</td>
                                    <td>
                                        {{ optional($booking->check_in_date)->format('M d, Y') ?? '-' }}
                                        <br>
                                        <small>to {{ optional($booking->check_out_date)->format('M d, Y') ?? '-' }}</small>
                                    </td>
                                    <td>{{ $booking->adults }} adult(s), {{ $booking->children }} child(ren)</td>
                                    <td>{{ $booking->rooms_count }}</td>
                                    <td>
                                        <span class="history-status status-{{ $booking->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex; flex-direction:column; gap:6px;">
                                            <span class="payment-status status-{{ $paymentStatus }}">
                                                {{ $paymentLabel }}
                                            </span>
                                            <small style="color:#94a3b8;">Attempts: {{ (int) ($booking->payment_attempts ?? 0) }} / 3</small>
                                            @if (in_array($paymentStatus, ['unpaid', 'rejected'], true) && in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true))
                                                <a class="btn btn-primary btn-sm" href="{{ route('rooms.payment', $booking) }}">Pay Now</a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ optional($booking->created_at)->format('M d, Y h:i A') ?? '-' }}</td>
                                    <td>{{ optional($booking->updated_at)->diffForHumans() ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="history-empty">No bookings found for your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($bookings->hasPages())
                    <div class="history-pagination">
                        <span>Showing {{ $bookings->firstItem() }}-{{ $bookings->lastItem() }} of {{ $bookings->total() }}</span>
                        <div class="history-pagination-links">
                            @if ($bookings->onFirstPage())
                                <span class="is-disabled">Previous</span>
                            @else
                                <a href="{{ $bookings->previousPageUrl() }}">Previous</a>
                            @endif
                            <span>Page {{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}</span>
                            @if ($bookings->hasMorePages())
                                <a href="{{ $bookings->nextPageUrl() }}">Next</a>
                            @else
                                <span class="is-disabled">Next</span>
                            @endif
                        </div>
                    </div>
                @endif
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
    </script>
</body>
</html>

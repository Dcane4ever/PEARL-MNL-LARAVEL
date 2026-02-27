<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pearl-user-id" content="{{ auth()->id() }}">
    <meta name="pearl-user-admin" content="{{ auth()->check() && auth()->user()->is_admin ? '1' : '0' }}">
    <title>Book a Room - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
    @vite(['resources/js/app.js'])
    <style>
        .customer-booking-topbar {
            display: grid;
            grid-template-columns: 1.7fr 1fr auto;
            gap: 12px;
            background: #0a3f91;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 18px;
        }
        .topbar-field {
            background: #fff;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 12px;
            min-height: 58px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }
        .topbar-field i {
            color: #64748b;
            font-size: 1.1rem;
        }
        .topbar-date-range {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            width: 100%;
        }
        .topbar-date-trigger {
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            color: #0f172a;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            font-size: .96rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .topbar-date-trigger .caret {
            margin-left: auto;
            color: #64748b;
            font-size: .86rem;
        }
        .topbar-date-hidden {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }
        .topbar-date-range input[type="date"].topbar-date-hidden {
            border: none;
            background: transparent;
            color: #0f172a;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            font-size: .96rem;
            min-width: 145px;
        }
        .topbar-date-range input[type="date"]:focus {
            outline: none;
        }
        .topbar-date-sep {
            color: #94a3b8;
            font-weight: 700;
        }
        .topbar-date-popup {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: min(640px, 94vw);
            z-index: 70;
            display: none;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.16);
            padding: 14px;
        }
        .topbar-date-popup.is-open {
            display: block;
        }
        .topbar-date-popup-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 8px;
        }
        .topbar-date-popup-nav {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #0f172a;
            cursor: pointer;
        }
        .topbar-date-months {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .topbar-date-month {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
        }
        .topbar-date-month-title {
            text-align: center;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .topbar-date-weekdays,
        .topbar-date-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
        }
        .topbar-date-weekdays span {
            text-align: center;
            font-size: .72rem;
            color: #64748b;
            font-weight: 600;
            padding: 3px 0;
        }
        .topbar-date-day {
            border: 1px solid transparent;
            background: #fff;
            border-radius: 8px;
            min-height: 34px;
            cursor: pointer;
            color: #0f172a;
            font-size: .84rem;
        }
        .topbar-date-day:hover:not(.is-disabled) {
            border-color: #93c5fd;
            background: #eff6ff;
        }
        .topbar-date-day.is-disabled {
            color: #cbd5e1;
            cursor: not-allowed;
        }
        .topbar-date-day.is-start,
        .topbar-date-day.is-end {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
        }
        .topbar-date-day.is-in-range {
            background: #dbeafe;
            color: #1e3a8a;
        }
        .topbar-date-day.is-unavailable:not(.is-start):not(.is-end) {
            color: #9ca3af;
            text-decoration: line-through;
        }
        .topbar-date-popup-foot {
            margin-top: 12px;
            border-top: 1px solid #eef2f7;
            padding-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .topbar-date-summary {
            font-size: .84rem;
            color: #475569;
            font-weight: 600;
        }
        .topbar-date-actions {
            display: inline-flex;
            gap: 8px;
        }
        .topbar-date-actions button {
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 600;
        }
        .topbar-date-clear {
            background: #e2e8f0;
            color: #0f172a;
        }
        .topbar-date-apply {
            background: #0a3f91;
            color: #fff;
        }
        .topbar-guests-trigger {
            border: none;
            background: transparent;
            text-align: left;
            padding: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            color: #0f172a;
            font-family: 'Poppins', sans-serif;
            font-size: .96rem;
            font-weight: 600;
        }
        .topbar-guests-trigger .caret {
            color: #64748b;
            font-size: .86rem;
        }
        .topbar-guest-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: min(360px, 92vw);
            z-index: 60;
            display: none;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.16);
            padding: 14px;
        }
        .topbar-guest-panel.is-open {
            display: block;
        }
        .topbar-guest-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .topbar-guest-row:last-of-type {
            border-bottom: none;
        }
        .topbar-guest-label strong {
            display: block;
            color: #0f172a;
            font-size: .95rem;
        }
        .topbar-guest-label small {
            color: #64748b;
            font-size: .78rem;
        }
        .topbar-counter {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .topbar-counter button {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #0a3f91;
            font-weight: 700;
            cursor: pointer;
        }
        .topbar-counter button:disabled {
            opacity: .45;
            cursor: not-allowed;
        }
        .topbar-child-ages {
            margin-top: 10px;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .topbar-child-age-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }
        .topbar-child-age-row span {
            font-size: .82rem;
            color: #64748b;
        }
        .topbar-child-age-row select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 5px 8px;
            font-family: 'Poppins', sans-serif;
            font-size: .82rem;
        }
        .topbar-actions {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }
        .topbar-actions button {
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            background: #0a3f91;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
        }
        .topbar-submit {
            border: none;
            border-radius: 10px;
            background: #0ea5e9;
            color: #fff;
            padding: 0 18px;
            min-height: 58px;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .topbar-book-submit {
            background: #2563eb;
        }
        .customer-day.is-search-selected {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.22);
        }
        @media (max-width: 960px) {
            .customer-booking-topbar {
                grid-template-columns: 1fr;
            }
            .topbar-submit {
                justify-content: center;
            }
            .topbar-date-popup {
                width: min(96vw, 640px);
                left: 50%;
                transform: translateX(-50%);
            }
            .topbar-date-months {
                grid-template-columns: 1fr;
            }
        }
        html.dark-theme .customer-booking-topbar {
            background: #0b2b63;
        }
        html.dark-theme .topbar-field,
        html.dark-theme .topbar-guest-panel {
            background: #0f172a;
            border-color: #334155;
        }
        html.dark-theme .topbar-date-range input[type="date"],
        html.dark-theme .topbar-date-trigger,
        html.dark-theme .topbar-guests-trigger,
        html.dark-theme .topbar-guest-label strong {
            color: #e2e8f0;
        }
        html.dark-theme .topbar-date-popup {
            background: #0f172a;
            border-color: #334155;
        }
        html.dark-theme .topbar-date-popup-nav,
        html.dark-theme .topbar-date-month,
        html.dark-theme .topbar-date-day,
        html.dark-theme .topbar-date-clear {
            background: #1e293b;
            border-color: #475569;
            color: #e2e8f0;
        }
        html.dark-theme .topbar-date-weekdays span,
        html.dark-theme .topbar-date-summary {
            color: #94a3b8;
        }
        html.dark-theme .topbar-date-day.is-in-range {
            background: #1e40af;
            color: #dbeafe;
        }
        html.dark-theme .topbar-date-day.is-unavailable:not(.is-start):not(.is-end) {
            color: #64748b;
        }
        html.dark-theme .topbar-date-month-title {
            color: #e2e8f0;
        }
        html.dark-theme .topbar-guest-label small,
        html.dark-theme .topbar-child-age-row span {
            color: #94a3b8;
        }
        html.dark-theme .topbar-counter button,
        html.dark-theme .topbar-child-age-row select {
            background: #1e293b;
            border-color: #475569;
            color: #e2e8f0;
        }

        .customer-booking-card {
            position: relative;
            overflow: visible;
        }

        .booking-pay-popup {
            position: absolute;
            right: 22px;
            top: 110px;
            width: min(320px, 92vw);
            background: #ffffff;
            border: 1px solid #dbe5f2;
            border-radius: 16px;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
            padding: 16px;
            z-index: 40;
            display: none;
        }
        .booking-pay-popup.is-open {
            display: block;
        }
        .booking-pay-popup h4 {
            margin: 0 0 6px;
            color: #0f172a;
            font-size: 1rem;
        }
        .booking-pay-popup p {
            margin: 0 0 12px;
            color: #64748b;
            font-size: 0.85rem;
        }
        .booking-pay-popup-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .booking-pay-popup .btn-outline {
            border: 1px solid #cbd5e1;
            color: #0f172a;
            background: #fff;
        }
        html.dark-theme .booking-pay-popup {
            background: #0f172a;
            border-color: #334155;
            box-shadow: 0 18px 40px rgba(2, 6, 23, 0.6);
        }
        html.dark-theme .booking-pay-popup h4 {
            color: #e2e8f0;
        }
        html.dark-theme .booking-pay-popup p {
            color: #94a3b8;
        }
        html.dark-theme .booking-pay-popup .btn-outline {
            background: #0b1220;
            border-color: #475569;
            color: #e2e8f0;
        }

        @media (max-width: 960px) {
            .booking-pay-popup {
                position: static;
                margin: 12px 0 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <section class="auth-page customer-booking-page">
        <div class="container customer-booking-container">
            <div class="customer-booking-card">
                <div class="customer-booking-topbar" id="customerBookingTopbar">
                    <div class="topbar-field">
                        <i class="far fa-calendar-alt"></i>
                        <div class="topbar-date-range">
                            <button type="button" class="topbar-date-trigger" id="topbarDateTrigger">
                                <span id="topbarDateSummary">Select check-in and check-out dates</span>
                                <i class="fas fa-chevron-down caret"></i>
                            </button>
                            <input type="date" class="topbar-date-hidden" id="topbarCheckInDate" min="{{ now()->toDateString() }}">
                            <input type="date" class="topbar-date-hidden" id="topbarCheckOutDate" min="{{ now()->toDateString() }}">
                        </div>
                        <div class="topbar-date-popup" id="topbarDatePopup">
                            <div class="topbar-date-popup-head">
                                <button type="button" class="topbar-date-popup-nav" id="topbarDatePrev" aria-label="Previous month">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="topbar-date-popup-nav" id="topbarDateNext" aria-label="Next month">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="topbar-date-months" id="topbarDateMonths"></div>
                            <div class="topbar-date-popup-foot">
                                <span class="topbar-date-summary" id="topbarDatePopupSummary">Select check-in and check-out</span>
                                <div class="topbar-date-actions">
                                    <button type="button" class="topbar-date-clear" id="topbarDateClear">Clear</button>
                                    <button type="button" class="topbar-date-apply" id="topbarDateApply">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="topbar-field">
                        <i class="far fa-user"></i>
                        <button type="button" class="topbar-guests-trigger" id="topbarGuestsTrigger">
                            <span id="topbarGuestsSummary">2 adults | 1 room</span>
                            <i class="fas fa-chevron-down caret"></i>
                        </button>
                        <div class="topbar-guest-panel" id="topbarGuestPanel">
                            <div class="topbar-guest-row">
                                <div class="topbar-guest-label">
                                    <strong>Adults</strong>
                                </div>
                                <div class="topbar-counter">
                                    <button type="button" id="topbarAdultsMinus">−</button>
                                    <span id="topbarAdultsValue">2</span>
                                    <button type="button" id="topbarAdultsPlus">+</button>
                                </div>
                            </div>
                            <div class="topbar-guest-row">
                                <div class="topbar-guest-label">
                                    <strong>Children</strong>
                                    <small>Ages 0-17</small>
                                </div>
                                <div class="topbar-counter">
                                    <button type="button" id="topbarChildrenMinus">−</button>
                                    <span id="topbarChildrenValue">0</span>
                                    <button type="button" id="topbarChildrenPlus">+</button>
                                </div>
                            </div>
                            <div class="topbar-child-ages" id="topbarChildAges" style="display:none;"></div>
                            <div class="topbar-guest-row">
                                <div class="topbar-guest-label">
                                    <strong>Rooms</strong>
                                </div>
                                <div class="topbar-counter">
                                    <button type="button" id="topbarRoomsMinus">−</button>
                                    <span id="topbarRoomsValue">1</span>
                                    <button type="button" id="topbarRoomsPlus">+</button>
                                </div>
                            </div>
                            <div class="topbar-actions">
                                <button type="button" id="topbarGuestDone">Done</button>
                            </div>
                        </div>
                    </div>

                    <button class="topbar-submit" type="button" id="topbarApplySearch">
                        <i class="fas fa-search"></i>
                        Check Availability
                    </button>
                    <button class="topbar-submit topbar-book-submit" type="button" id="topbarBookNow">
                        <i class="fas fa-calendar-check"></i>
                        Book Selected
                    </button>
                </div>

                <div class="customer-booking-head">
                    <div>
                        <h2>Booking Calendar</h2>
                        <p>Use the top bar to set your booking, then use this calendar to inspect room availability by day.</p>
                    </div>
                    <button class="btn btn-outline" type="button" id="jumpToday">Jump to Today</button>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                  @if (!empty($confirmedBookings) && $confirmedBookings->count() > 0)
                      @php
                          $bookingIds = $confirmedBookings->pluck('id')->toArray();
                          $confirmedPaymentMap = $confirmedBookings->pluck('payment_status', 'id')->toArray();
                      @endphp
                      <div class="alert alert-success" id="bookingConfirmedAlert" data-booking-ids="{{ json_encode($bookingIds) }}" data-payment-map="{{ json_encode($confirmedPaymentMap) }}">
                          Booking is confirmed, please proceed to pay online or through the front desk.
                      </div>
                  @endif

                @if (!empty($cancelledBookings) && $cancelledBookings->count() > 0)
                    @php
                        $cancelledIds = $cancelledBookings->pluck('id')->toArray();
                    @endphp
                    <div class="alert alert-error" id="bookingCancelledAlert" data-booking-ids="{{ json_encode($cancelledIds) }}">
                        Your booking is cancelled, contact the support desk for more information.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $calendarMonths = $calendarDays->groupBy(fn ($day) => $day->format('Y-m'));
                @endphp
                <div class="customer-month-nav" id="customerMonthNav">
                    <button class="customer-month-nav-btn" type="button" id="customerMonthPrev">
                        <i class="fas fa-chevron-left"></i>
                        <span>Previous</span>
                    </button>
                    <div class="customer-month-nav-title-wrap">
                        <h4 class="customer-month-nav-title" id="customerMonthTitle"></h4>
                        <p class="customer-month-nav-subtitle">Monthly view</p>
                    </div>
                    <button class="customer-month-nav-btn" type="button" id="customerMonthNext">
                        <span>Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div id="bookingCalendar">
                    @foreach ($calendarMonths as $monthDays)
                        @php
                            $monthLabel = $monthDays->first()->format('F Y');
                            $monthKey = $monthDays->first()->format('Y-m');
                        @endphp
                        <div class="customer-month-block {{ $loop->first ? 'is-active' : '' }}" data-month-label="{{ $monthLabel }}" data-month-key="{{ $monthKey }}">
                            <div class="customer-calendar-grid">
                                @foreach ($monthDays as $day)
                                    @php
                                        $dateKey = $day->toDateString();
                                        $dayAvailability = $availabilityByDate[$dateKey] ?? [];
                                        $bookable = collect($dayAvailability)->contains(fn ($item) => ($item['available'] ?? 0) > 0);
                                    @endphp
                                    <button
                                        class="customer-day {{ $bookable ? 'is-open' : 'is-closed' }}"
                                        type="button"
                                        data-date="{{ $dateKey }}"
                                        data-bookable="{{ $bookable ? '1' : '0' }}"
                                        @foreach ($rooms as $room)
                                            @php $roomState = $dayAvailability[$room->id] ?? ['available' => 0, 'capacity' => 0]; @endphp
                                            data-room-{{ $room->id }}-available="{{ $roomState['available'] }}"
                                            data-room-{{ $room->id }}-capacity="{{ $roomState['capacity'] }}"
                                        @endforeach
                                    >
                                        <div class="customer-day-top">
                                            <span class="customer-day-weekday">{{ $day->format('D') }}</span>
                                            <span class="customer-day-number">{{ $day->format('d') }}</span>
                                            <span class="customer-day-month">{{ $day->format('M') }}</span>
                                        </div>
                                        <div class="customer-day-rooms">
                                            @foreach ($rooms as $room)
                                                @php $roomState = $dayAvailability[$room->id] ?? ['available' => 0, 'capacity' => 0]; @endphp
                                                <div class="customer-room-line {{ ($roomState['available'] ?? 0) > 0 ? 'has-stock' : 'no-stock' }}">
                                                    <span>{{ $room->name }}</span>
                                                    <strong>{{ $roomState['available'] ?? 0 }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if (! $bookable)
                                            <span class="customer-day-badge">Unavailable</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="customer-modal" id="bookingModal" aria-hidden="true">
        <div class="customer-modal-backdrop" data-close-modal="bookingModal"></div>
        
        <!-- Floor Availability Panel -->
        <div class="floor-availability-panel" id="floorAvailabilityPanel">
            <div class="floor-availability-header">
                <h4><i class="fas fa-building"></i> Floor Availability</h4>
                <p id="floorAvailabilityDate">Select a date</p>
            </div>
            <div class="floor-availability-body" id="floorAvailabilityBody">
                <p class="floor-availability-empty">Click a date to view floor-by-floor availability</p>
            </div>
        </div>

        <div class="customer-modal-card" role="dialog" aria-modal="true" aria-labelledby="booking-modal-title">
            <div class="customer-modal-head">
                <div>
                    <h3 id="booking-modal-title">New Booking</h3>
                    <p>Select room type and complete your reservation details.</p>
                </div>
                <button class="customer-modal-close" type="button" data-close-modal="bookingModal">&times;</button>
            </div>

            <form class="auth-form" method="POST" action="{{ route('rooms.booking.store') }}" id="bookingForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="check_in_date" id="modalCheckInDate">

                <div>
                    <label for="modalDateDisplay">Check-in Date</label>
                    <input type="text" id="modalDateDisplay" readonly>
                </div>

                <div>
                    <label for="modalRoomId">Room Type</label>
                    <select id="modalRoomId" name="room_id" required>
                        <option value="">Choose room type</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" data-room-id="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <small id="roomAvailabilityNote" class="customer-note">Select a room to view availability.</small>
                </div>

                <div>
                    <label for="modalAdults">Adults</label>
                    <input id="modalAdults" name="adults" type="number" min="1" max="10" required value="2">
                </div>

                <div>
                    <label for="modalChildren">Children</label>
                    <input id="modalChildren" name="children" type="number" min="0" max="10" required value="0">
                </div>

                <div>
                    <label for="modalRoomsCount">Rooms</label>
                    <input id="modalRoomsCount" name="rooms_count" type="number" min="1" max="10" required value="1">
                </div>

                <div>
                    <label for="modalCheckOutDate">Check-out Date</label>
                    <input id="modalCheckOutDate" name="check_out_date" type="date" min="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                </div>

                <div>
                    <label for="modalCheckInTime">Check-in Time</label>
                    <input id="modalCheckInTime" name="check_in_time" type="time" required value="14:00">
                </div>

                <div>
                    <label for="modalCheckOutTime">Check-out Time</label>
                    <input id="modalCheckOutTime" name="check_out_time" type="time" required value="12:00">
                </div>

                <div>
                    <label for="modalIdDocument">Valid ID Images (JPG, PNG) - up to 6 files</label>
                    <input id="modalIdDocument" name="id_documents[]" type="file" accept=".jpg,.jpeg,.png" multiple required>
                    <small class="customer-note">Upload 1 to 6 ID images. Each file must be 5MB or less.</small>
                </div>

                <div class="auth-actions">
                    <span></span>
                    <button class="btn btn-primary" type="submit" id="bookingSubmit">Complete Reservation</button>
                </div>
            </form>
        </div>
    </div>

    <div class="booking-confirm-popup" id="bookingConfirmPopup" aria-hidden="true">
        <div class="booking-confirm-popup-backdrop" data-close-popup="bookingConfirmPopup"></div>
        <div class="booking-confirm-popup-card" id="bookingConfirmCard" role="dialog" aria-modal="true" aria-labelledby="booking-confirm-title">
            <div class="booking-confirm-popup-icon" id="bookingConfirmIconWrap" aria-hidden="true">
                <i class="fas fa-circle-check"></i>
            </div>
            <h3 id="booking-confirm-title">Booking Update</h3>
            <p id="bookingConfirmMessage">Booking is confirmed, please proceed to pay online or through the front desk.</p>
            <button class="btn btn-primary booking-confirm-popup-btn" type="button" id="bookingConfirmClose">Got it</button>
        </div>
    </div>
    <div class="booking-pay-popup" id="bookingPayPopup" aria-hidden="true">
        <h4>Pay Now Online</h4>
        <p id="bookingPayMessage">Proceed to the QR payment page to upload your proof of payment.</p>
        <div class="booking-pay-popup-actions">
            <button class="btn btn-outline btn-sm" type="button" id="bookingPayDismiss">Later</button>
            <a class="btn btn-primary btn-sm" id="bookingPayLink" href="#">Pay Now</a>
        </div>
    </div>

    <script src="{{ asset('js/test.js') }}"></script>
    <script>
        const bookingCalendar = document.getElementById('bookingCalendar');
        const modal = document.getElementById('bookingModal');
        const closeControls = document.querySelectorAll('[data-close-modal="bookingModal"]');
        const checkInDate = document.getElementById('modalCheckInDate');
        const dateDisplay = document.getElementById('modalDateDisplay');
        const checkOutDate = document.getElementById('modalCheckOutDate');
        const roomSelect = document.getElementById('modalRoomId');
        const roomAvailabilityNote = document.getElementById('roomAvailabilityNote');
        const bookingSubmit = document.getElementById('bookingSubmit');
        const jumpToday = document.getElementById('jumpToday');
        const floorAvailabilityPanel = document.getElementById('floorAvailabilityPanel');
        const floorAvailabilityBody = document.getElementById('floorAvailabilityBody');
        const floorAvailabilityDate = document.getElementById('floorAvailabilityDate');
        const customerMonthBlocks = Array.from(document.querySelectorAll('.customer-month-block'));
        const customerMonthTitle = document.getElementById('customerMonthTitle');
        const customerMonthPrev = document.getElementById('customerMonthPrev');
        const customerMonthNext = document.getElementById('customerMonthNext');
        const bookingConfirmPopup = document.getElementById('bookingConfirmPopup');
        const bookingConfirmCard = document.getElementById('bookingConfirmCard');
        const bookingConfirmTitle = document.getElementById('booking-confirm-title');
        const bookingConfirmIconWrap = document.getElementById('bookingConfirmIconWrap');
        const bookingConfirmMessage = document.getElementById('bookingConfirmMessage');
        const bookingConfirmClose = document.getElementById('bookingConfirmClose');
        const bookingConfirmBackdrop = document.querySelector('[data-close-popup="bookingConfirmPopup"]');
        const bookingPayPopup = document.getElementById('bookingPayPopup');
        const bookingPayMessage = document.getElementById('bookingPayMessage');
        const bookingPayLink = document.getElementById('bookingPayLink');
        const bookingPayDismiss = document.getElementById('bookingPayDismiss');
        const todayDateKey = @json(\Carbon\Carbon::today()->toDateString());
        const paymentBaseUrl = @json(url('/rooms/payment'));
        const topbarCheckInDate = document.getElementById('topbarCheckInDate');
        const topbarCheckOutDate = document.getElementById('topbarCheckOutDate');
        const topbarDateTrigger = document.getElementById('topbarDateTrigger');
        const topbarDateSummary = document.getElementById('topbarDateSummary');
        const topbarDatePopup = document.getElementById('topbarDatePopup');
        const topbarDateMonths = document.getElementById('topbarDateMonths');
        const topbarDatePrev = document.getElementById('topbarDatePrev');
        const topbarDateNext = document.getElementById('topbarDateNext');
        const topbarDatePopupSummary = document.getElementById('topbarDatePopupSummary');
        const topbarDateClear = document.getElementById('topbarDateClear');
        const topbarDateApply = document.getElementById('topbarDateApply');
        const topbarGuestsTrigger = document.getElementById('topbarGuestsTrigger');
        const topbarGuestsSummary = document.getElementById('topbarGuestsSummary');
        const topbarGuestPanel = document.getElementById('topbarGuestPanel');
        const topbarAdultsMinus = document.getElementById('topbarAdultsMinus');
        const topbarAdultsPlus = document.getElementById('topbarAdultsPlus');
        const topbarAdultsValue = document.getElementById('topbarAdultsValue');
        const topbarChildrenMinus = document.getElementById('topbarChildrenMinus');
        const topbarChildrenPlus = document.getElementById('topbarChildrenPlus');
        const topbarChildrenValue = document.getElementById('topbarChildrenValue');
        const topbarRoomsMinus = document.getElementById('topbarRoomsMinus');
        const topbarRoomsPlus = document.getElementById('topbarRoomsPlus');
        const topbarRoomsValue = document.getElementById('topbarRoomsValue');
        const topbarChildAges = document.getElementById('topbarChildAges');
        const topbarGuestDone = document.getElementById('topbarGuestDone');
        const topbarApplySearch = document.getElementById('topbarApplySearch');
        const topbarBookNow = document.getElementById('topbarBookNow');

        let topbarAdults = Number(document.getElementById('modalAdults')?.value || 2);
        let topbarChildren = Number(document.getElementById('modalChildren')?.value || 0);
        let topbarRooms = Number(document.getElementById('modalRoomsCount')?.value || 1);
        let topbarChildrenAges = [];
        let activeBookingDate = null;
        let popupMonthCursor = null;
        let popupMode = 'checkin';

        let activeDay = null;
        let activeCustomerMonthIndex = customerMonthBlocks.findIndex((block) => block.classList.contains('is-active'));

        if (activeCustomerMonthIndex < 0 && customerMonthBlocks.length > 0) {
            activeCustomerMonthIndex = 0;
            customerMonthBlocks[0].classList.add('is-active');
        }

        // Floor inventory data from server
        const floorInventoryData = @json($floorInventoryData);
        const availabilityByDate = @json($availabilityByDate);
        const rooms = @json($rooms);

        const formatDateLabel = (value) => {
            const date = new Date(value + 'T00:00:00');
            return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        };

        const addDays = (dateKey, days) => {
            const date = new Date(dateKey + 'T00:00:00');
            date.setDate(date.getDate() + days);
            return date.toISOString().split('T')[0];
        };
        const MAX_STAY_DAYS = 90;
        const getMaxCheckoutDateKey = (checkInKey) => {
            if (!checkInKey) return '';
            return addDays(checkInKey, MAX_STAY_DAYS);
        };

        const toDateKey = (dateObject) => {
            return new Date(Date.UTC(dateObject.getFullYear(), dateObject.getMonth(), dateObject.getDate()))
                .toISOString()
                .split('T')[0];
        };

        const parseDateKey = (dateKey) => {
            return dateKey ? new Date(dateKey + 'T00:00:00') : null;
        };

        const formatCompactDateLabel = (dateKey) => {
            if (!dateKey) return '';
            const date = parseDateKey(dateKey);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        };

        const formatTopbarDateSummary = () => {
            if (!topbarCheckInDate?.value) {
                return 'Select check-in and check-out dates';
            }
            if (!topbarCheckOutDate?.value) {
                return `${formatCompactDateLabel(topbarCheckInDate.value)} — Select check-out`;
            }
            const nights = Math.max(0, Math.round((parseDateKey(topbarCheckOutDate.value) - parseDateKey(topbarCheckInDate.value)) / (1000 * 60 * 60 * 24)));
            if (nights === 0) {
                return `${formatCompactDateLabel(topbarCheckInDate.value)} - ${formatCompactDateLabel(topbarCheckOutDate.value)} (same-day stay)`;
            }
            return `${formatCompactDateLabel(topbarCheckInDate.value)} - ${formatCompactDateLabel(topbarCheckOutDate.value)} (${nights}-night stay)`;
        };

        const syncCheckoutBounds = () => {
            if (!topbarCheckInDate || !topbarCheckOutDate) {
                return;
            }

            if (!topbarCheckInDate.value) {
                topbarCheckOutDate.min = '';
                topbarCheckOutDate.max = '';
                return;
            }

            const minCheckOut = topbarCheckInDate.value;
            const maxCheckOut = getMaxCheckoutDateKey(minCheckOut);
            topbarCheckOutDate.min = minCheckOut;
            topbarCheckOutDate.max = maxCheckOut;

            if (topbarCheckOutDate.value > maxCheckOut) {
                topbarCheckOutDate.value = maxCheckOut;
            }
        };

        const isDateBeforeToday = (dateKey) => {
            return dateKey < todayDateKey;
        };

        const isBookableCheckInDate = (dateKey) => {
            if (!dateKey || isDateBeforeToday(dateKey)) {
                return false;
            }

            const dayAvailability = availabilityByDate?.[dateKey] ?? {};
            return Object.values(dayAvailability).some((roomState) => Number(roomState?.available ?? 0) > 0);
        };

        const updateDateSummaryLabels = () => {
            const summaryText = formatTopbarDateSummary();
            if (topbarDateSummary) {
                topbarDateSummary.textContent = summaryText;
            }
            if (topbarDatePopupSummary) {
                topbarDatePopupSummary.textContent = topbarCheckInDate?.value && topbarCheckOutDate?.value
                    ? `${formatDateLabel(topbarCheckInDate.value)} -> ${formatDateLabel(topbarCheckOutDate.value)}`
                    : 'Select check-in and check-out';
            }
        };

        const isInSelectedRange = (dateKey) => {
            if (!topbarCheckInDate?.value || !topbarCheckOutDate?.value) {
                return false;
            }
            return dateKey > topbarCheckInDate.value && dateKey < topbarCheckOutDate.value;
        };

        const renderPopupMonth = (baseDate) => {
            const monthStart = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
            const monthName = monthStart.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            const startWeekday = monthStart.getDay();
            const daysInMonth = new Date(baseDate.getFullYear(), baseDate.getMonth() + 1, 0).getDate();

            let daysMarkup = '';
            for (let i = 0; i < startWeekday; i += 1) {
                daysMarkup += '<span></span>';
            }

            for (let dayNumber = 1; dayNumber <= daysInMonth; dayNumber += 1) {
                const dateObject = new Date(baseDate.getFullYear(), baseDate.getMonth(), dayNumber);
                const dateKey = toDateKey(dateObject);
                const isDisabledByPast = isDateBeforeToday(dateKey);
                const isSelectingCheckIn = !topbarCheckInDate?.value || popupMode === 'checkin';
                const isDisabledByAvailability = isSelectingCheckIn && !isBookableCheckInDate(dateKey);
                const isCheckoutCandidateBlocked = !isSelectingCheckIn && topbarCheckInDate?.value && dateKey < topbarCheckInDate.value;
                const maxCheckoutDate = !isSelectingCheckIn && topbarCheckInDate?.value
                    ? getMaxCheckoutDateKey(topbarCheckInDate.value)
                    : '';
                const isCheckoutCandidateTooFar = !isSelectingCheckIn && maxCheckoutDate && dateKey > maxCheckoutDate;
                const isDisabled = isDisabledByPast || isDisabledByAvailability || isCheckoutCandidateBlocked || isCheckoutCandidateTooFar;

                const cssClasses = [
                    'topbar-date-day',
                    isSelectingCheckIn && !isBookableCheckInDate(dateKey) ? 'is-unavailable' : '',
                    topbarCheckInDate?.value === dateKey ? 'is-start' : '',
                    topbarCheckOutDate?.value === dateKey ? 'is-end' : '',
                    isInSelectedRange(dateKey) ? 'is-in-range' : '',
                    isDisabled ? 'is-disabled' : ''
                ].filter(Boolean).join(' ');

                daysMarkup += `<button type="button" class="${cssClasses}" data-popup-date="${dateKey}">${dayNumber}</button>`;
            }

            return `
                <div class="topbar-date-month">
                    <div class="topbar-date-month-title">${monthName}</div>
                    <div class="topbar-date-weekdays">
                        <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                    </div>
                    <div class="topbar-date-days">${daysMarkup}</div>
                </div>
            `;
        };

        const renderDatePopup = () => {
            if (!topbarDateMonths || !popupMonthCursor) {
                return;
            }

            const secondMonthCursor = new Date(popupMonthCursor.getFullYear(), popupMonthCursor.getMonth() + 1, 1);
            topbarDateMonths.innerHTML = `${renderPopupMonth(popupMonthCursor)}${renderPopupMonth(secondMonthCursor)}`;

            topbarDateMonths.querySelectorAll('[data-popup-date]').forEach((buttonElement) => {
                buttonElement.addEventListener('click', () => {
                    if (buttonElement.classList.contains('is-disabled')) return;
                    const selectedDate = buttonElement.getAttribute('data-popup-date');
                    if (!selectedDate) return;

                    if (!topbarCheckInDate.value || popupMode === 'checkin') {
                    if (!isBookableCheckInDate(selectedDate)) {
                        return;
                    }
                    topbarCheckInDate.value = selectedDate;
                    topbarCheckOutDate.value = '';
                    popupMode = 'checkout';
                } else if (selectedDate >= topbarCheckInDate.value && selectedDate <= getMaxCheckoutDateKey(topbarCheckInDate.value)) {
                    topbarCheckOutDate.value = selectedDate;
                } else {
                    if (!isBookableCheckInDate(selectedDate)) {
                        return;
                    }
                    topbarCheckInDate.value = selectedDate;
                    topbarCheckOutDate.value = '';
                    popupMode = 'checkout';
                }

                    syncCheckoutBounds();
                    updateDateSummaryLabels();
                    renderDatePopup();
                });
            });
        };

        const formatGuestSummary = () => {
            const childLabel = topbarChildren === 1 ? 'child' : 'children';
            const roomLabel = topbarRooms === 1 ? 'room' : 'rooms';
            if (topbarChildren > 0) {
                return `${topbarAdults} adults | ${topbarChildren} ${childLabel} | ${topbarRooms} ${roomLabel}`;
            }
            return `${topbarAdults} adults | ${topbarRooms} ${roomLabel}`;
        };

        const updateTopbarSummary = () => {
            topbarAdultsValue.textContent = String(topbarAdults);
            topbarChildrenValue.textContent = String(topbarChildren);
            topbarRoomsValue.textContent = String(topbarRooms);
            topbarGuestsSummary.textContent = formatGuestSummary();

            topbarAdultsMinus.disabled = topbarAdults <= 1;
            topbarChildrenMinus.disabled = topbarChildren <= 0;
            topbarRoomsMinus.disabled = topbarRooms <= 1;

            while (topbarChildrenAges.length < topbarChildren) {
                topbarChildrenAges.push(0);
            }
            while (topbarChildrenAges.length > topbarChildren) {
                topbarChildrenAges.pop();
            }

            if (topbarChildren === 0) {
                topbarChildAges.style.display = 'none';
                topbarChildAges.innerHTML = '';
                return;
            }

            topbarChildAges.style.display = 'block';
            topbarChildAges.innerHTML = topbarChildrenAges.map((age, index) => `
                <div class="topbar-child-age-row">
                    <span>Child ${index + 1}</span>
                    <select data-child-age-index="${index}">
                        ${Array.from({ length: 18 }, (_, year) => `<option value="${year}" ${year === age ? 'selected' : ''}>${year} years old</option>`).join('')}
                    </select>
                </div>
            `).join('');

            topbarChildAges.querySelectorAll('select[data-child-age-index]').forEach((selectElement) => {
                selectElement.addEventListener('change', (event) => {
                    const childIndex = Number(event.target.getAttribute('data-child-age-index'));
                    topbarChildrenAges[childIndex] = Number(event.target.value);
                });
            });
        };

        const syncTopbarValuesToModal = () => {
            const adultsInput = document.getElementById('modalAdults');
            const childrenInput = document.getElementById('modalChildren');
            const roomsInput = document.getElementById('modalRoomsCount');

            if (adultsInput) adultsInput.value = String(topbarAdults);
            if (childrenInput) childrenInput.value = String(topbarChildren);
            if (roomsInput) roomsInput.value = String(topbarRooms);
        };

        const focusDateInCalendar = (dateKey) => {
            if (!dateKey) return;

            const targetDay = bookingCalendar.querySelector(`.customer-day[data-date="${dateKey}"]`);
            bookingCalendar.querySelectorAll('.customer-day.is-search-selected').forEach((dayElement) => {
                dayElement.classList.remove('is-search-selected');
            });

            if (!targetDay) return;

            const monthBlock = targetDay.closest('.customer-month-block');
            const monthIndex = customerMonthBlocks.findIndex((block) => block === monthBlock);
            if (monthIndex >= 0 && monthIndex !== activeCustomerMonthIndex) {
                activeCustomerMonthIndex = monthIndex;
                renderCustomerMonth();
            }

            targetDay.classList.add('is-search-selected');
            targetDay.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        };

        const renderCustomerMonth = () => {
            customerMonthBlocks.forEach((block, index) => {
                block.classList.toggle('is-active', index === activeCustomerMonthIndex);
            });

            const activeMonth = customerMonthBlocks[activeCustomerMonthIndex];
            if (customerMonthTitle && activeMonth) {
                customerMonthTitle.textContent = activeMonth.dataset.monthLabel || '';
            }

            if (customerMonthPrev) {
                customerMonthPrev.disabled = activeCustomerMonthIndex <= 0;
            }

            if (customerMonthNext) {
                customerMonthNext.disabled = activeCustomerMonthIndex >= customerMonthBlocks.length - 1;
            }
        };

        customerMonthPrev?.addEventListener('click', () => {
            if (activeCustomerMonthIndex > 0) {
                activeCustomerMonthIndex -= 1;
                renderCustomerMonth();
            }
        });

        customerMonthNext?.addEventListener('click', () => {
            if (activeCustomerMonthIndex < customerMonthBlocks.length - 1) {
                activeCustomerMonthIndex += 1;
                renderCustomerMonth();
            }
        });

        renderCustomerMonth();

        if (topbarCheckInDate && topbarCheckOutDate) {
            topbarCheckInDate.value = '';
            topbarCheckOutDate.value = '';
            topbarCheckOutDate.min = '';
            topbarCheckOutDate.max = '';
            const initialDateCursor = parseDateKey(todayDateKey);
            popupMonthCursor = new Date(initialDateCursor.getFullYear(), initialDateCursor.getMonth(), 1);

            topbarCheckInDate.addEventListener('change', () => {
                if (!topbarCheckInDate.value) {
                    topbarCheckOutDate.min = '';
                    topbarCheckOutDate.max = '';
                    updateDateSummaryLabels();
                    renderDatePopup();
                    return;
                }

                syncCheckoutBounds();
                updateDateSummaryLabels();
                renderDatePopup();
            });
        }

        updateDateSummaryLabels();
        renderDatePopup();

        updateTopbarSummary();
        syncTopbarValuesToModal();

        topbarAdultsMinus?.addEventListener('click', () => {
            if (topbarAdults > 1) {
                topbarAdults -= 1;
                updateTopbarSummary();
                syncTopbarValuesToModal();
            }
        });

        topbarAdultsPlus?.addEventListener('click', () => {
            if (topbarAdults < 10) {
                topbarAdults += 1;
                updateTopbarSummary();
                syncTopbarValuesToModal();
            }
        });

        topbarChildrenMinus?.addEventListener('click', () => {
            if (topbarChildren > 0) {
                topbarChildren -= 1;
                updateTopbarSummary();
                syncTopbarValuesToModal();
            }
        });

        topbarChildrenPlus?.addEventListener('click', () => {
            if (topbarChildren < 10) {
                topbarChildren += 1;
                updateTopbarSummary();
                syncTopbarValuesToModal();
            }
        });

        topbarRoomsMinus?.addEventListener('click', () => {
            if (topbarRooms > 1) {
                topbarRooms -= 1;
                updateTopbarSummary();
                syncTopbarValuesToModal();
            }
        });

        topbarRoomsPlus?.addEventListener('click', () => {
            if (topbarRooms < 10) {
                topbarRooms += 1;
                updateTopbarSummary();
                syncTopbarValuesToModal();
            }
        });

        topbarGuestsTrigger?.addEventListener('click', () => {
            topbarGuestPanel?.classList.toggle('is-open');
        });

        topbarGuestDone?.addEventListener('click', () => {
            topbarGuestPanel?.classList.remove('is-open');
        });

        topbarApplySearch?.addEventListener('click', () => {
            topbarGuestPanel?.classList.remove('is-open');
            syncTopbarValuesToModal();

            const selectedCheckIn = topbarCheckInDate?.value;
            const selectedCheckOut = topbarCheckOutDate?.value;
            if (!selectedCheckIn || !selectedCheckOut || selectedCheckOut < selectedCheckIn) {
                topbarDatePopup?.classList.add('is-open');
                return;
            }
            if (selectedCheckOut > getMaxCheckoutDateKey(selectedCheckIn)) {
                topbarDatePopup?.classList.add('is-open');
                topbarCheckOutDate?.focus();
                return;
            }

            activeBookingDate = selectedCheckIn || null;
            focusDateInCalendar(selectedCheckIn);
        });

        topbarBookNow?.addEventListener('click', () => {
            topbarGuestPanel?.classList.remove('is-open');
            syncTopbarValuesToModal();

            const selectedCheckIn = topbarCheckInDate?.value;
            const selectedCheckOut = topbarCheckOutDate?.value;

            if (!selectedCheckIn) {
                topbarCheckInDate?.focus();
                return;
            }

            if (!selectedCheckOut || selectedCheckOut < selectedCheckIn) {
                topbarCheckOutDate?.focus();
                return;
            }
            if (selectedCheckOut > getMaxCheckoutDateKey(selectedCheckIn)) {
                topbarCheckOutDate?.focus();
                return;
            }

            activeBookingDate = selectedCheckIn;
            focusDateInCalendar(selectedCheckIn);

            checkInDate.value = selectedCheckIn;
            dateDisplay.value = formatDateLabel(selectedCheckIn);
            checkOutDate.value = selectedCheckOut;
            checkOutDate.min = selectedCheckIn;
            checkOutDate.max = getMaxCheckoutDateKey(selectedCheckIn);
            roomSelect.value = '';
            roomAvailabilityNote.textContent = 'Select a room to view availability.';
            bookingSubmit.disabled = true;

            updateFloorAvailability(selectedCheckIn);
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });

        topbarDateTrigger?.addEventListener('click', () => {
            topbarGuestPanel?.classList.remove('is-open');
            topbarDatePopup?.classList.toggle('is-open');
            popupMode = topbarCheckInDate?.value ? 'checkout' : 'checkin';
            renderDatePopup();
        });

        topbarDatePrev?.addEventListener('click', () => {
            if (!popupMonthCursor) return;
            popupMonthCursor = new Date(popupMonthCursor.getFullYear(), popupMonthCursor.getMonth() - 1, 1);
            renderDatePopup();
        });

        topbarDateNext?.addEventListener('click', () => {
            if (!popupMonthCursor) return;
            popupMonthCursor = new Date(popupMonthCursor.getFullYear(), popupMonthCursor.getMonth() + 1, 1);
            renderDatePopup();
        });

        topbarDateClear?.addEventListener('click', () => {
            topbarCheckInDate.value = '';
            topbarCheckOutDate.value = '';
            topbarCheckOutDate.min = '';
            topbarCheckOutDate.max = '';
            popupMode = 'checkin';
            updateDateSummaryLabels();
            renderDatePopup();
        });

        topbarDateApply?.addEventListener('click', () => {
            if (topbarCheckInDate.value && topbarCheckOutDate.value && topbarCheckOutDate.value >= topbarCheckInDate.value) {
                topbarDatePopup?.classList.remove('is-open');
                updateDateSummaryLabels();
            }
        });

        document.addEventListener('click', (event) => {
            if (!topbarGuestPanel || !topbarGuestsTrigger) {
                return;
            }

            const clickedInsidePanel = topbarGuestPanel.contains(event.target);
            const clickedTrigger = topbarGuestsTrigger.contains(event.target);

            if (!clickedInsidePanel && !clickedTrigger) {
                topbarGuestPanel.classList.remove('is-open');
            }

            if (topbarDatePopup && topbarDateTrigger) {
                const clickedInsideDatePopup = topbarDatePopup.contains(event.target);
                const clickedDateTrigger = topbarDateTrigger.contains(event.target);
                if (!clickedInsideDatePopup && !clickedDateTrigger) {
                    topbarDatePopup.classList.remove('is-open');
                }
            }
        });

        const updateFloorAvailability = (date) => {
            floorAvailabilityDate.textContent = formatDateLabel(date);
            
            const inventoryForDate = floorInventoryData[date] || [];
            
            if (inventoryForDate.length === 0) {
                floorAvailabilityBody.innerHTML = '<p class="floor-availability-empty">No rooms available on this date</p>';
                return;
            }

            // Group by room type
            const roomGroups = {};
            inventoryForDate.forEach(item => {
                if (!roomGroups[item.room_id]) {
                    roomGroups[item.room_id] = [];
                }
                roomGroups[item.room_id].push(item);
            });

            let html = '';
            Object.keys(roomGroups).forEach(roomId => {
                const room = rooms.find(r => r.id == roomId);
                const floors = roomGroups[roomId];
                
                html += `
                    <div class="floor-room-group">
                        <div class="floor-room-header">
                            <i class="fas fa-bed"></i>
                            <span>${room ? room.name : 'Room'}</span>
                        </div>
                        <div class="floor-list">
                `;
                
                floors.forEach(floor => {
                    html += `
                        <div class="floor-item">
                            <span class="floor-label">Floor ${floor.floor.number}</span>
                            <span class="floor-count">${floor.available_rooms} available</span>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            });

            floorAvailabilityBody.innerHTML = html;
        };

        const openModal = (dayButton) => {
            activeDay = dayButton;
            const date = dayButton.dataset.date;
            activeBookingDate = date;
            checkInDate.value = date;
            dateDisplay.value = formatDateLabel(date);
            const selectedTopbarCheckOut = topbarCheckOutDate?.value;
            const minimumCheckOut = date;
            const maximumCheckOut = getMaxCheckoutDateKey(date);
            checkOutDate.value = selectedTopbarCheckOut && selectedTopbarCheckOut >= date && selectedTopbarCheckOut <= maximumCheckOut
                ? selectedTopbarCheckOut
                : minimumCheckOut;
            checkOutDate.min = minimumCheckOut;
            checkOutDate.max = maximumCheckOut;
            roomSelect.value = '';
            roomAvailabilityNote.textContent = 'Select a room to view availability.';
            bookingSubmit.disabled = true;

            syncTopbarValuesToModal();
            
            // Update floor availability
            updateFloorAvailability(date);
            
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        };

        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        };

        const openBookingConfirmPopup = ({ title, message, type = 'confirmed' }) => {
            if (!bookingConfirmPopup) {
                return;
            }

            if (bookingConfirmCard) {
                bookingConfirmCard.classList.toggle('is-cancelled', type === 'cancelled');
            }

            if (bookingConfirmIconWrap) {
                bookingConfirmIconWrap.innerHTML = type === 'cancelled'
                    ? '<i class="fas fa-circle-exclamation"></i>'
                    : '<i class="fas fa-circle-check"></i>';
            }

            if (bookingConfirmTitle) {
                bookingConfirmTitle.textContent = title;
            }

            if (bookingConfirmMessage) {
                bookingConfirmMessage.textContent = message;
            }

            bookingConfirmPopup.classList.add('is-open');
            bookingConfirmPopup.setAttribute('aria-hidden', 'false');
        };

        const openBookingPayPopup = ({ bookingId, paymentStatus }) => {
            if (!bookingPayPopup) {
                return;
            }

            if (paymentStatus === 'submitted') {
                bookingPayMessage.textContent = 'Payment submitted. You can review the payment status anytime.';
            } else if (paymentStatus === 'rejected') {
                bookingPayMessage.textContent = 'Payment was rejected. Please upload a new payment screenshot.';
            } else {
                bookingPayMessage.textContent = 'Proceed to the QR payment page to upload your proof of payment.';
            }

            if (bookingPayLink) {
                bookingPayLink.href = bookingId ? `${paymentBaseUrl}/${bookingId}` : paymentBaseUrl;
            }

            bookingPayPopup.classList.add('is-open');
            bookingPayPopup.setAttribute('aria-hidden', 'false');
        };

        const closeBookingConfirmPopup = () => {
            if (!bookingConfirmPopup) {
                return;
            }

            bookingConfirmPopup.classList.remove('is-open');
            bookingConfirmPopup.setAttribute('aria-hidden', 'true');
        };

        const closeBookingPayPopup = () => {
            if (!bookingPayPopup) {
                return;
            }
            bookingPayPopup.classList.remove('is-open');
            bookingPayPopup.setAttribute('aria-hidden', 'true');
        };

        bookingConfirmClose?.addEventListener('click', closeBookingConfirmPopup);
        bookingConfirmBackdrop?.addEventListener('click', closeBookingConfirmPopup);
        bookingPayDismiss?.addEventListener('click', closeBookingPayPopup);
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeBookingConfirmPopup();
                closeBookingPayPopup();
            }
        });

        bookingCalendar.querySelectorAll('.customer-day').forEach((button) => {
            button.addEventListener('click', () => {
                const selectedDate = button.dataset.date;
                if (!selectedDate) {
                    return;
                }

                const canUseAsCheckIn = isBookableCheckInDate(selectedDate);
                const currentCheckIn = topbarCheckInDate?.value || '';
                const canUseAsCheckoutOnly = Boolean(currentCheckIn)
                    && selectedDate >= currentCheckIn
                    && selectedDate <= getMaxCheckoutDateKey(currentCheckIn);

                if (canUseAsCheckIn && topbarCheckInDate && topbarCheckOutDate) {
                    topbarCheckInDate.value = selectedDate;
                    const computedCheckOut = selectedDate;
                    topbarCheckOutDate.min = computedCheckOut;
                    topbarCheckOutDate.max = getMaxCheckoutDateKey(computedCheckOut);
                    if (!topbarCheckOutDate.value || topbarCheckOutDate.value < selectedDate) {
                        topbarCheckOutDate.value = computedCheckOut;
                    }
                    if (topbarCheckOutDate.value > topbarCheckOutDate.max) {
                        topbarCheckOutDate.value = topbarCheckOutDate.max;
                    }
                } else if (canUseAsCheckoutOnly && topbarCheckOutDate) {
                    topbarCheckOutDate.value = selectedDate;
                } else {
                    return;
                }

                updateDateSummaryLabels();
                renderDatePopup();

                activeBookingDate = selectedDate;
                syncTopbarValuesToModal();
                focusDateInCalendar(selectedDate);
            });
        });

        closeControls.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        roomSelect.addEventListener('change', () => {
            const selectedDate = checkInDate.value || activeBookingDate || topbarCheckInDate?.value;

            if (!selectedDate || !roomSelect.value) {
                bookingSubmit.disabled = true;
                roomAvailabilityNote.textContent = 'Select a room to view availability.';
                return;
            }

            const roomState = availabilityByDate?.[selectedDate]?.[roomSelect.value] ?? { available: 0, capacity: 0 };
            const available = Number(roomState.available ?? 0);
            const capacity = Number(roomState.capacity ?? 0);

            if (capacity <= 0) {
                roomAvailabilityNote.textContent = 'Admin has not set inventory for this room yet.';
                bookingSubmit.disabled = true;
                return;
            }

            roomAvailabilityNote.textContent = available > 0
                ? `${available} room(s) available for selected date.`
                : 'No rooms available for selected date.';
            bookingSubmit.disabled = available <= 0;
        });

        jumpToday.addEventListener('click', () => {
            const todayButton = bookingCalendar.querySelector(`.customer-day[data-date="${todayDateKey}"]`);
            if (todayButton) {
                const monthBlock = todayButton.closest('.customer-month-block');
                const monthIndex = customerMonthBlocks.findIndex((block) => block === monthBlock);

                if (monthIndex >= 0 && monthIndex !== activeCustomerMonthIndex) {
                    activeCustomerMonthIndex = monthIndex;
                    renderCustomerMonth();
                }

                todayButton.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
                return;
            }

            const firstDay = bookingCalendar.querySelector('.customer-month-block.is-active .customer-day');
            if (firstDay) {
                firstDay.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            }
        });

        // Handle booking status messages with localStorage + real-time polling
        const bookingAlert = document.getElementById('bookingConfirmedAlert');
        const bookingCancelledAlert = document.getElementById('bookingCancelledAlert');
        const confirmedStorageKey = 'acknowledgedConfirmedBookings';
        const cancelledStorageKey = 'acknowledgedCancelledBookings';
        const submittedStorageKey = 'acknowledgedSubmittedBookings';
        const legacyStorageKey = 'acknowledgedBookings';
        const bookingConfirmedPopupMessage = 'Booking is confirmed, please proceed to pay online or through the front desk.';
        const bookingCancelledPopupMessage = 'Your booking is cancelled, contact the support desk for more information.';
        const confirmationsEndpoint = @json(route('rooms.booking.confirmations'));

        const getAcknowledgedBookings = (storageKey, includeLegacy = false) => {
            try {
                const stored = JSON.parse(localStorage.getItem(storageKey) || '[]');
                const parsed = Array.isArray(stored)
                    ? stored.map((id) => Number(id)).filter((id) => Number.isFinite(id))
                    : [];

                if (!includeLegacy) {
                    return parsed;
                }

                const legacyStored = JSON.parse(localStorage.getItem(legacyStorageKey) || '[]');
                const parsedLegacy = Array.isArray(legacyStored)
                    ? legacyStored.map((id) => Number(id)).filter((id) => Number.isFinite(id))
                    : [];

                return [...new Set([...parsed, ...parsedLegacy])];
            } catch (error) {
                return [];
            }
        };

        const setAcknowledgedBookings = (storageKey, bookingIds) => {
            localStorage.setItem(storageKey, JSON.stringify(bookingIds));
        };

        const showBookingStatusPopup = ({ storageKey, alertElement, message, title, type, newBookingIds, showPopup = true, includeLegacy = false, paymentMap = {} }) => {
            if (!newBookingIds.length) {
                return;
            }

            if (alertElement) {
                alertElement.textContent = message;
                alertElement.style.display = 'block';
            }

            const acknowledgedBookings = getAcknowledgedBookings(storageKey, includeLegacy);
            const updatedAcknowledged = [...new Set([...acknowledgedBookings, ...newBookingIds])];
            setAcknowledgedBookings(storageKey, updatedAcknowledged);

            if (showPopup) {
                openBookingConfirmPopup({ title, message, type });
            }

            if (type === 'confirmed') {
                const nextBookingId = newBookingIds[0];
                const paymentStatus = paymentMap?.[nextBookingId] || 'unpaid';
                openBookingPayPopup({ bookingId: nextBookingId, paymentStatus });
            }
        };

        if (bookingAlert) {
            const initialBookingIds = JSON.parse(bookingAlert.dataset.bookingIds || '[]')
                .map((id) => Number(id))
                .filter((id) => Number.isFinite(id));
            const paymentMap = JSON.parse(bookingAlert.dataset.paymentMap || '{}') || {};
            const acknowledgedBookings = getAcknowledgedBookings(confirmedStorageKey, true);
            const newInitialBookingIds = initialBookingIds.filter((id) => !acknowledgedBookings.includes(id));

            if (newInitialBookingIds.length > 0) {
                showBookingStatusPopup({
                    storageKey: confirmedStorageKey,
                    alertElement: bookingAlert,
                    message: bookingConfirmedPopupMessage,
                    title: 'Booking Confirmed',
                    type: 'confirmed',
                    newBookingIds: newInitialBookingIds,
                    showPopup: true,
                    includeLegacy: true,
                    paymentMap
                });
            } else {
                bookingAlert.style.display = 'none';
            }
        }

        if (bookingCancelledAlert) {
            const initialCancelledIds = JSON.parse(bookingCancelledAlert.dataset.bookingIds || '[]')
                .map((id) => Number(id))
                .filter((id) => Number.isFinite(id));
            const acknowledgedCancelled = getAcknowledgedBookings(cancelledStorageKey);
            const newInitialCancelledIds = initialCancelledIds.filter((id) => !acknowledgedCancelled.includes(id));

            if (newInitialCancelledIds.length > 0) {
                showBookingStatusPopup({
                    storageKey: cancelledStorageKey,
                    alertElement: bookingCancelledAlert,
                    message: bookingCancelledPopupMessage,
                    title: 'Booking Cancelled',
                    type: 'cancelled',
                    newBookingIds: newInitialCancelledIds,
                    showPopup: true
                });
            } else {
                bookingCancelledAlert.style.display = 'none';
            }
        }

        const bookingSubmissionMessage = @json(session('status'));
        const submittedBookingId = Number(@json(session('submitted_booking_id')));
        if (bookingSubmissionMessage) {
            if (Number.isFinite(submittedBookingId)) {
                const acknowledgedSubmitted = getAcknowledgedBookings(submittedStorageKey);
                if (!acknowledgedSubmitted.includes(submittedBookingId)) {
                    setAcknowledgedBookings(submittedStorageKey, [...new Set([...acknowledgedSubmitted, submittedBookingId])]);
                    openBookingConfirmPopup({
                        title: 'Booking Submitted',
                        message: bookingSubmissionMessage,
                        type: 'submitted'
                    });
                }
            } else {
                openBookingConfirmPopup({
                    title: 'Booking Submitted',
                    message: bookingSubmissionMessage,
                    type: 'submitted'
                });
            }
        }

        let isPollingConfirmations = false;
        const pollBookingConfirmations = async () => {
            if (isPollingConfirmations || document.visibilityState === 'hidden') {
                return;
            }

            isPollingConfirmations = true;
            try {
                const response = await fetch(confirmationsEndpoint, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const confirmedBookingIds = Array.isArray(payload.confirmed_booking_ids)
                    ? payload.confirmed_booking_ids.map((id) => Number(id)).filter((id) => Number.isFinite(id))
                    : [];
                const confirmedPaymentMap = Array.isArray(payload.confirmed_bookings)
                    ? payload.confirmed_bookings.reduce((acc, booking) => {
                        if (Number.isFinite(Number(booking?.id))) {
                            acc[Number(booking.id)] = booking.payment_status || 'unpaid';
                        }
                        return acc;
                    }, {})
                    : {};
                const cancelledBookingIds = Array.isArray(payload.cancelled_booking_ids)
                    ? payload.cancelled_booking_ids.map((id) => Number(id)).filter((id) => Number.isFinite(id))
                    : [];

                const acknowledgedConfirmed = getAcknowledgedBookings(confirmedStorageKey, true);
                const newConfirmedIds = confirmedBookingIds.filter((id) => !acknowledgedConfirmed.includes(id));
                if (newConfirmedIds.length > 0) {
                    showBookingStatusPopup({
                        storageKey: confirmedStorageKey,
                        alertElement: bookingAlert,
                        message: bookingConfirmedPopupMessage,
                        title: 'Booking Confirmed',
                        type: 'confirmed',
                        newBookingIds: newConfirmedIds,
                        showPopup: true,
                        includeLegacy: true,
                        paymentMap: confirmedPaymentMap
                    });
                }

                const acknowledgedCancelled = getAcknowledgedBookings(cancelledStorageKey);
                const newCancelledIds = cancelledBookingIds.filter((id) => !acknowledgedCancelled.includes(id));
                if (newCancelledIds.length > 0) {
                    showBookingStatusPopup({
                        storageKey: cancelledStorageKey,
                        alertElement: bookingCancelledAlert,
                        message: bookingCancelledPopupMessage,
                        title: 'Booking Cancelled',
                        type: 'cancelled',
                        newBookingIds: newCancelledIds,
                        showPopup: true
                    });
                }
            } catch (error) {
                console.error('Unable to fetch booking confirmations.', error);
            } finally {
                isPollingConfirmations = false;
            }
        };

        const confirmationPollingInterval = window.setInterval(pollBookingConfirmations, 15000);
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                pollBookingConfirmations();
            }
        });
        pollBookingConfirmations();

        window.PearlLiveRefresh = (payload = {}) => {
            if (payload?.scope === 'booking' || payload?.scope === 'payment' || payload?.scope === 'poll') {
                pollBookingConfirmations();
                return;
            }

            // Inventory updates change availability; reload to refresh calendar + floor inventory.
            if (payload?.scope === 'inventory') {
                window.location.reload();
                return;
            }

            window.location.reload();
        };

        window.dispatchEvent(new Event('pearl:live-ready'));

        // Keep acknowledgement keys on logout so popups show once per booking/device.
    </script>
</body>
</html>


<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Models\Booking;
use App\Models\DailyRoomInventory;
use App\Models\Floor;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/rooms', function () {
    return view('rooms.index');
});

Route::get('/facilities', function () {
    return view('facilities.index');
});

Route::get('/checkin', function () {
    return view('checkin.index');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    if ($user && ! $user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    return redirect()->route('rooms.booking');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/rooms/booking', function () {
        $bookableFloorNumbers = [15, 16];
        foreach ($bookableFloorNumbers as $floorNumber) {
            Floor::firstOrCreate(
                ['number' => $floorNumber],
                ['label' => 'Floor '.$floorNumber]
            );
        }
        $bookableFloorIds = Floor::whereIn('number', $bookableFloorNumbers)->pluck('id');

        Booking::where('status', 'checkout_scheduled')
            ->whereNotNull('checkout_release_available_at')
            ->where('checkout_release_available_at', '<=', now())
            ->update([
                'status' => 'checked_out',
                'checkout_release_state' => 'released_now',
                'checkout_released_at' => now(),
            ]);

        $rooms = Room::where('is_active', true)
            ->whereIn('slug', ['junior-suite', 'superior-king'])
            ->orderByRaw("FIELD(slug, 'junior-suite', 'superior-king')")
            ->get();

        if ($rooms->count() === 0) {
            $rooms = Room::where('is_active', true)->orderBy('name')->limit(2)->get();
        }

        $startDate = Carbon::today();
        $endDate = Carbon::today()->copy()->addMonthNoOverflow()->endOfMonth();
        $calendarDays = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $calendarDays->push($date->copy());
        }
        $calendarDateKeys = $calendarDays->map(fn ($day) => $day->toDateString());

        // Strict per-date inventory: only dates explicitly set by admin are available.
        $allInventory = DailyRoomInventory::whereIn('room_id', $rooms->pluck('id'))
            ->whereIn('floor_id', $bookableFloorIds)
            ->whereIn('inventory_date', $calendarDateKeys->toArray())
            ->get();

        $dailyCapacityRows = $allInventory->groupBy(function ($item) {
            return $item->room_id.'-'.$item->inventory_date;
        })->map(function ($group) {
            return (object) [
                'room_id' => $group->first()->room_id,
                'inventory_date' => $group->first()->inventory_date,
                'total_rooms' => $group->sum('available_rooms'),
            ];
        });

        // Tracks the latest admin inventory update per room/date.
        // Used to let admins explicitly re-open dates after a cancellation.
        $latestInventoryUpdateByRoomDate = $allInventory->groupBy(function ($item) {
            return $item->room_id.'-'.$item->inventory_date;
        })->map(function ($group) {
            $latestUpdated = $group->max('updated_at');
            return $latestUpdated ? Carbon::parse($latestUpdated)->getTimestamp() : null;
        });

        $floorInventoryData = DailyRoomInventory::with(['floor', 'room'])
            ->whereIn('room_id', $rooms->pluck('id'))
            ->whereIn('floor_id', $bookableFloorIds)
            ->whereIn('inventory_date', $calendarDateKeys->toArray())
            ->where('available_rooms', '>', 0)
            ->get()
            ->groupBy('inventory_date');

        $activeBookings = Booking::whereIn('room_id', $rooms->pluck('id'))
            // Cancelled bookings remain blocked for their originally booked stay dates.
            ->whereIn('status', ['pending', 'pending_verification', 'confirmed', 'checked_in', 'checkout_scheduled', 'cancelled'])
            ->whereDate('check_in_date', '<=', $endDate->toDateString())
            ->whereDate('check_out_date', '>=', $startDate->toDateString())
            ->get();

        $bookedCounts = collect();
        foreach ($activeBookings as $booking) {
            $checkIn = Carbon::parse($booking->check_in_date)->startOfDay();
            $checkOut = Carbon::parse($booking->check_out_date)->startOfDay();

            $defaultEnd = $checkOut->copy()->subDay();
            if ($checkOut->lessThanOrEqualTo($checkIn)) {
                $defaultEnd = $checkIn->copy();
            }

            $blockEnd = $defaultEnd->copy();

            if (in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true)) {
                if ($booking->checkout_release_state === 'released_now') {
                    $blockEnd = $defaultEnd->copy();
                } elseif ($booking->checkout_release_state === 'scheduled' && !empty($booking->checkout_release_available_at)) {
                    $releaseDate = Carbon::parse($booking->checkout_release_available_at)->startOfDay();
                    $scheduledEnd = $releaseDate->copy()->subDay();
                    if ($scheduledEnd->gt($blockEnd)) {
                        $blockEnd = $scheduledEnd;
                    }
                } else {
                    $blockEnd = $checkOut->copy();
                }
            }

            if ($blockEnd->lt($checkIn)) {
                continue;
            }

            $rangeStart = $checkIn->copy()->max($startDate);
            $rangeEnd = $blockEnd->copy()->min($endDate);
            if ($rangeEnd->lt($rangeStart)) {
                continue;
            }

            for ($dateCursor = $rangeStart->copy(); $dateCursor->lte($rangeEnd); $dateCursor->addDay()) {
                $bucketKey = $booking->room_id.'-'.$dateCursor->toDateString();

                if ($booking->status === 'cancelled') {
                    $cancelledAtTs = optional($booking->updated_at)->getTimestamp();
                    $inventoryUpdatedTs = $latestInventoryUpdateByRoomDate->get($bucketKey);

                    // Manual override: if admin updated inventory after cancellation, release this date.
                    if ($cancelledAtTs && $inventoryUpdatedTs && $inventoryUpdatedTs >= $cancelledAtTs) {
                        continue;
                    }
                }

                $bookedCounts[$bucketKey] = ($bookedCounts[$bucketKey] ?? 0) + (int) $booking->rooms_count;
            }
        }

        $availabilityByDate = [];
        foreach ($calendarDays as $day) {
            $dateKey = $day->toDateString();
            $availabilityByDate[$dateKey] = [];

            foreach ($rooms as $room) {
                $capacity = (int) ($dailyCapacityRows->get($room->id.'-'.$dateKey)?->total_rooms ?? 0);
                $booked = (int) ($bookedCounts[$room->id.'-'.$dateKey] ?? 0);
                $available = max($capacity - $booked, 0);

                $availabilityByDate[$dateKey][$room->id] = [
                    'capacity' => $capacity,
                    'booked' => $booked,
                    'available' => $available,
                ];
            }
        }

        $confirmedBookings = auth()->user()
            ?->bookings()
            ->where('status', 'confirmed')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->latest()
            ->get();

        $cancelledBookings = auth()->user()
            ?->bookings()
            ->where('status', 'cancelled')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->latest()
            ->get();

        return view('rooms.booking', compact('rooms', 'confirmedBookings', 'cancelledBookings', 'calendarDays', 'availabilityByDate', 'floorInventoryData'));
    })->middleware('verified')->name('rooms.booking');

    Route::post('/rooms/booking', [BookingController::class, 'store'])
        ->middleware('verified')
        ->middleware('throttle:8,1')
        ->name('rooms.booking.store');

    Route::get('/rooms/payment/{booking}', [BookingController::class, 'showPayment'])
        ->middleware('verified')
        ->name('rooms.payment');

    Route::post('/rooms/payment/{booking}', [BookingController::class, 'submitPaymentProof'])
        ->middleware('verified')
        ->middleware('throttle:8,1')
        ->name('rooms.payment.submit');

    Route::get('/rooms/booking/confirmations', [BookingController::class, 'confirmations'])
        ->middleware('verified')
        ->name('rooms.booking.confirmations');

    Route::get('/rooms/history', function () {
        $historyQuery = auth()->user()
            ->bookings()
            ->with('room');

        $statusFilter = trim((string) request('status_filter', ''));
        $allowedStatuses = ['pending', 'pending_verification', 'confirmed', 'checked_in', 'checkout_scheduled', 'checked_out', 'cancelled'];
        if (in_array($statusFilter, $allowedStatuses, true)) {
            $historyQuery->where('status', $statusFilter);
        }

        $referenceFilter = trim((string) request('reference', ''));
        if ($referenceFilter !== '' && ctype_digit($referenceFilter)) {
            $historyQuery->where('id', (int) $referenceFilter);
        }

        $checkInFrom = trim((string) request('check_in_from', ''));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkInFrom)) {
            $historyQuery->whereDate('check_in_date', '>=', $checkInFrom);
        }

        $checkOutTo = trim((string) request('check_out_to', ''));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkOutTo)) {
            $historyQuery->whereDate('check_out_date', '<=', $checkOutTo);
        }

        $bookings = $historyQuery
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('rooms.history', compact('bookings', 'statusFilter', 'referenceFilter', 'checkInFrom', 'checkOutTo'));
    })->middleware('verified')->name('rooms.history');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('admin')->name('admin.dashboard');

    Route::get('/admin/operations', function () {
        $bookableFloorNumbers = [15, 16];
        foreach ($bookableFloorNumbers as $floorNumber) {
            Floor::firstOrCreate(
                ['number' => $floorNumber],
                ['label' => 'Floor '.$floorNumber]
            );
        }

        Booking::where('status', 'checkout_scheduled')
            ->whereNotNull('checkout_release_available_at')
            ->where('checkout_release_available_at', '<=', now())
            ->update([
                'status' => 'checked_out',
                'checkout_release_state' => 'released_now',
                'checkout_released_at' => now(),
            ]);

        $inventoryDate = request('inventory_date', Carbon::today()->toDateString());
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $inventoryDate)) {
            $inventoryDate = Carbon::today()->toDateString();
        } elseif (Carbon::parse($inventoryDate)->lt(Carbon::today()->startOfDay())) {
            $inventoryDate = Carbon::today()->toDateString();
        }
        $openInventory = request()->boolean('open_inventory');

        $calendarStart = Carbon::today()->copy()->startOfMonth();
        $calendarEnd = Carbon::today()->copy()->addMonthNoOverflow()->endOfMonth();
        $calendarDays = collect();

        for ($date = $calendarStart->copy(); $date->lte($calendarEnd); $date->addDay()) {
            $calendarDays->push($date->copy());
        }

        $rooms = Room::orderBy('name')->get();
        $inventoryRooms = Room::whereIn('slug', ['junior-suite', 'superior-king'])
            ->orderByRaw("FIELD(slug, 'junior-suite', 'superior-king')")
            ->get();
        if ($inventoryRooms->isEmpty()) {
            $inventoryRooms = Room::where('is_active', true)->orderBy('name')->limit(2)->get();
        }

        $floors = Floor::whereIn('number', $bookableFloorNumbers)->orderBy('number')->get();
        $bookableFloorIds = $floors->pluck('id');

        $superiorFloorLimit = 11;
        $juniorFloorLimit = 4;
        $floorTotalLimit = 15;
        $roomPerFloorLimits = [];
        foreach ($inventoryRooms as $inventoryRoom) {
            if ($inventoryRoom->slug === 'superior-king' || str_contains(strtolower($inventoryRoom->name), 'superior')) {
                $roomPerFloorLimits[$inventoryRoom->id] = $superiorFloorLimit;
                continue;
            }
            if ($inventoryRoom->slug === 'junior-suite' || str_contains(strtolower($inventoryRoom->name), 'junior')) {
                $roomPerFloorLimits[$inventoryRoom->id] = $juniorFloorLimit;
                continue;
            }
            $roomPerFloorLimits[$inventoryRoom->id] = $floorTotalLimit;
        }

        $inventory = DailyRoomInventory::whereDate('inventory_date', '>=', $calendarStart->toDateString())
            ->whereDate('inventory_date', '<=', $calendarEnd->toDateString())
            ->whereIn('floor_id', $bookableFloorIds)
            ->whereIn('room_id', $inventoryRooms->pluck('id'))
            ->get()
            ->keyBy(fn ($item) => $item->inventory_date.'|'.$item->floor_id.'-'.$item->room_id);
        $pendingBookings = Booking::with(['user', 'room', 'verifiedByAdmin'])
            ->whereIn('status', ['pending', 'pending_verification'])
            ->latest()
            ->get();
        $confirmedBookings = Booking::with(['user', 'room', 'verifiedByAdmin'])
            ->whereIn('status', ['confirmed', 'checked_in', 'checkout_scheduled'])
            ->latest()
            ->get();
        $cancelledBookings = Booking::with(['user', 'room', 'verifiedByAdmin'])
            ->where('status', 'cancelled')
            ->latest()
            ->get();
        $checkoutQueue = Booking::with(['user', 'room', 'checkoutReleasedByAdmin'])
            ->whereIn('status', ['confirmed', 'checked_in', 'checkout_scheduled'])
            ->whereDate('check_out_date', '<=', Carbon::today()->toDateString())
            ->orderBy('check_out_date')
            ->orderBy('check_out_time')
            ->get();
        
        // Booking History with filters, search, and sorting
        $historyQuery = Booking::with(['user', 'room', 'verifiedByAdmin']);
        
        // Status filter
        $statusFilter = request('status_filter');
        if ($statusFilter && in_array($statusFilter, ['pending', 'pending_verification', 'confirmed', 'checked_in', 'checkout_scheduled', 'checked_out', 'cancelled'])) {
            $historyQuery->where('status', $statusFilter);
        }
        
        // Search by customer name or email
        $search = request('search');
        if ($search) {
            $historyQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Sorting
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'check_in_date', 'check_out_date', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $historyQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $historyQuery->latest();
        }
        
        $bookingHistory = $historyQuery->get();
        $selectedBooking = null;
        $selectedBookingId = (string) request('selected_booking', '');
        if ($selectedBookingId !== '' && ctype_digit($selectedBookingId)) {
            $selectedBooking = $bookingHistory->firstWhere('id', (int) $selectedBookingId);
        }
        if (! $selectedBooking && $bookingHistory->isNotEmpty()) {
            $selectedBooking = $bookingHistory->first();
        }

        $selectedBookingIdViewed = false;
        $selectedBookingPaymentViewed = false;
        if ($selectedBooking && auth()->check()) {
            $selectedBookingIdViewed = \App\Models\BookingAudit::where('booking_id', $selectedBooking->id)
                ->where('actor_user_id', auth()->id())
                ->where('action', 'id_document_viewed')
                ->exists();
            $selectedBookingPaymentViewed = \App\Models\BookingAudit::where('booking_id', $selectedBooking->id)
                ->where('actor_user_id', auth()->id())
                ->where('action', 'payment_proof_viewed')
                ->exists();
        }

        return view('admin.operations', compact(
            'rooms',
            'inventoryRooms',
            'floors',
            'inventory',
            'pendingBookings',
            'confirmedBookings',
            'cancelledBookings',
            'checkoutQueue',
            'bookingHistory',
            'selectedBooking',
            'inventoryDate',
            'openInventory',
            'calendarDays',
            'superiorFloorLimit',
            'juniorFloorLimit',
            'floorTotalLimit',
            'roomPerFloorLimits',
            'selectedBookingIdViewed',
            'selectedBookingPaymentViewed'
        ));
    })->middleware('admin')->name('admin.operations');

    Route::get('/admin/users', function () {
        return view('admin.users');
    })->middleware('admin')->name('admin.users');

    Route::post('/admin/users', [AdminController::class, 'storeAdmin'])
        ->middleware('admin')
        ->name('admin.users.store');

    Route::post('/admin/bookings/{booking}/confirm', [AdminController::class, 'confirmBooking'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.confirm');

    Route::post('/admin/bookings/{booking}/check-in', [AdminController::class, 'checkInBooking'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.check-in');

    Route::post('/admin/bookings/{booking}/verify', [AdminController::class, 'verifyBooking'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.verify');

    Route::get('/admin/bookings/{booking}/id-document', [AdminController::class, 'downloadBookingId'])
        ->middleware(['admin', 'throttle:60,1'])
        ->name('admin.bookings.id-document');

    Route::get('/admin/bookings/{booking}/payment-proof', [AdminController::class, 'downloadPaymentProof'])
        ->middleware(['admin', 'throttle:60,1'])
        ->name('admin.bookings.payment-proof');

    Route::post('/admin/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.cancel');

    Route::post('/admin/bookings/{booking}/payment-verify', [AdminController::class, 'verifyPayment'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.payment-verify');

    Route::post('/admin/bookings/{booking}/payment-reject', [AdminController::class, 'rejectPayment'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.payment-reject');

    Route::post('/admin/bookings/{booking}/checkout-release-now', [AdminController::class, 'releaseCheckoutNow'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.checkout-release-now');

    Route::post('/admin/bookings/{booking}/checkout-release-schedule', [AdminController::class, 'scheduleCheckoutRelease'])
        ->middleware(['admin', 'throttle:30,1'])
        ->name('admin.bookings.checkout-release-schedule');

    Route::post('/admin/inventory', [AdminController::class, 'updateInventory'])
        ->middleware('admin')
        ->name('admin.inventory.update');

    Route::post('/admin/rooms', [AdminController::class, 'updateRooms'])
        ->middleware('admin')
        ->name('admin.rooms.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

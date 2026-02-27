<?php

namespace App\Http\Controllers;

use App\Events\AdminOpsUpdated;
use App\Events\BookingUpdated;
use App\Events\InventoryUpdated;
use App\Mail\BookingStatusMail;
use App\Models\Booking;
use App\Models\BookingAudit;
use App\Models\DailyRoomInventory;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function storeAdmin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => true,
        ]);

        return back()->with('admin_created', 'Admin user created.');
    }

    public function confirmBooking(Booking $booking): RedirectResponse
    {
        $hasIdDocument = !empty($booking->id_document_path)
            || !empty($booking->id_document_paths)
            || (is_array($booking->id_document_paths) && count($booking->id_document_paths) > 0);

        if (empty($booking->verified_at) || ! $hasIdDocument) {
            return back()->withErrors([
                'status' => 'Booking must be ID-verified before confirmation.',
            ]);
        }

        $oldStatus = $booking->status;
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => $booking->payment_status ?: 'unpaid',
        ]);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => request()->user()?->id,
            'action' => 'booking_confirmed',
            'old_status' => $oldStatus,
            'new_status' => 'confirmed',
            'notes' => 'Admin confirmed booking after verification.',
        ]);

        $booking->loadMissing(['user', 'room']);
        $this->sendBookingEmail(
            $booking,
            'Booking Confirmed - Proceed to Payment',
            'Your booking has been confirmed. Please proceed to pay online or through the front desk.'
        );

        return back()->with('status', 'Booking confirmed.');
    }

    public function checkInBooking(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status === 'checked_in') {
            return back()->withErrors([
                'status' => 'This booking is already marked as checked in.',
            ]);
        }

        if (! in_array($booking->status, ['confirmed'], true)) {
            return back()->withErrors([
                'status' => 'Only confirmed bookings can be checked in.',
            ]);
        }

        $today = Carbon::today()->startOfDay();
        $checkInDate = Carbon::parse($booking->check_in_date)->startOfDay();
        if ($checkInDate->gt($today)) {
            return back()->withErrors([
                'status' => 'Guest cannot be checked in before the check-in date.',
            ]);
        }

        $oldStatus = $booking->status;
        $booking->update([
            'status' => 'checked_in',
            'checkout_release_state' => null,
            'checkout_release_available_at' => null,
            'checkout_released_at' => null,
            'checkout_release_admin_id' => null,
        ]);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()?->id,
            'action' => 'booking_checked_in',
            'old_status' => $oldStatus,
            'new_status' => 'checked_in',
            'notes' => 'Admin marked booking as checked in.',
        ]);

        $booking->loadMissing(['user', 'room']);
        $this->sendBookingEmail(
            $booking,
            'Guest Checked In',
            'Your booking is now marked as checked in. We hope you enjoy your stay at The Pearl Manila Hotel.'
        );

        return back()->with('status', 'Guest checked in successfully.');
    }

    public function verifyBooking(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $hasIdDocument = !empty($booking->id_document_path)
            || !empty($booking->id_document_paths)
            || (is_array($booking->id_document_paths) && count($booking->id_document_paths) > 0);

        if (! $hasIdDocument) {
            return back()->withErrors([
                'status' => 'No ID document uploaded for this booking.',
            ]);
        }

        $adminId = $request->user()?->id;
        $hasViewedId = BookingAudit::where('booking_id', $booking->id)
            ->where('actor_user_id', $adminId)
            ->where('action', 'id_document_viewed')
            ->exists();

        if (! $hasViewedId) {
            return back()->withErrors([
                'status' => 'Please view the ID document before verifying this booking.',
            ]);
        }

        $oldStatus = $booking->status;
        $booking->update([
            'status' => 'pending_verification',
            'verified_by_admin_id' => $request->user()->id,
            'verified_at' => now(),
            'verification_notes' => $data['verification_notes'] ?? null,
        ]);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'id_verified',
            'old_status' => $oldStatus,
            'new_status' => 'pending_verification',
            'notes' => $data['verification_notes'] ?? 'ID verification completed by admin.',
        ]);

        $booking->loadMissing(['user', 'room']);
        $this->sendBookingEmail(
            $booking,
            'Booking ID Verified - Awaiting Final Confirmation',
            'Your submitted ID has been verified by our admin team. Your booking is now awaiting final confirmation.'
        );

        return back()->with('status', 'Booking ID verified. You can now confirm or cancel this booking.');
    }

    public function downloadBookingId(Request $request, Booking $booking): StreamedResponse|RedirectResponse
    {
        $documentPaths = [];

        if (is_array($booking->id_document_paths) && count($booking->id_document_paths) > 0) {
            $documentPaths = array_values(array_filter($booking->id_document_paths));
        }

        if (empty($documentPaths) && !empty($booking->id_document_path)) {
            $documentPaths = [$booking->id_document_path];
        }

        $selectedFileIndex = max((int) $request->query('file', 0), 0);
        $idDocumentPath = $documentPaths[$selectedFileIndex] ?? null;

        if (empty($idDocumentPath) && count($documentPaths) > 0) {
            $idDocumentPath = $documentPaths[0];
        }

        if (empty($idDocumentPath) || !Storage::disk('private')->exists($idDocumentPath)) {
            return back()->withErrors([
                'status' => 'ID document file is unavailable for this booking.',
            ]);
        }

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => request()->user()?->id,
            'action' => 'id_document_viewed',
            'old_status' => $booking->status,
            'new_status' => $booking->status,
            'notes' => 'Admin downloaded/viewed uploaded ID document.',
        ]);

        if ($request->boolean('preview')) {
            return Storage::disk('private')->response($idDocumentPath);
        }

        return Storage::disk('private')->download($idDocumentPath);
    }

    public function downloadPaymentProof(Request $request, Booking $booking): StreamedResponse|RedirectResponse
    {
        $paymentProofPath = $booking->payment_proof_path;

        if (empty($paymentProofPath) || !Storage::disk('private')->exists($paymentProofPath)) {
            return back()->withErrors([
                'status' => 'Payment proof file is unavailable for this booking.',
            ]);
        }

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => request()->user()?->id,
            'action' => 'payment_proof_viewed',
            'old_status' => $booking->status,
            'new_status' => $booking->status,
            'notes' => 'Admin downloaded/viewed uploaded payment proof.',
        ]);

        if ($request->boolean('preview')) {
            return Storage::disk('private')->response($paymentProofPath);
        }

        return Storage::disk('private')->download($paymentProofPath);
    }

    public function verifyPayment(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->payment_status !== 'submitted') {
            return back()->withErrors([
                'payment_status' => 'Only submitted payments can be verified.',
            ]);
        }

        $adminId = $request->user()?->id;
        $hasViewedPayment = BookingAudit::where('booking_id', $booking->id)
            ->where('actor_user_id', $adminId)
            ->where('action', 'payment_proof_viewed')
            ->exists();

        if (! $hasViewedPayment) {
            return back()->withErrors([
                'payment_status' => 'Please view the payment proof before verifying this payment.',
            ]);
        }

        $data = $request->validate([
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldPaymentStatus = $booking->payment_status;
        $booking->update([
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'payment_verified_by' => $request->user()->id,
            'payment_notes' => $data['payment_notes'] ?? null,
        ]);

        event(new BookingUpdated($booking, 'payment'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'payment_verified',
            'old_status' => $booking->status,
            'new_status' => $booking->status,
            'notes' => 'Admin verified online payment.',
            'context' => [
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => 'verified',
            ],
        ]);

        $booking->loadMissing(['user', 'room']);
        $this->sendBookingEmail(
            $booking,
            'Payment Accepted - Booking Confirmed',
            'Payment accepted. Please proceed to your booking details. Thank you for choosing The Pearl Manila Hotel.'
        );

        return back()->with('status', 'Payment marked as verified.');
    }

    public function rejectPayment(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->payment_status !== 'submitted') {
            return back()->withErrors([
                'payment_status' => 'Only submitted payments can be rejected.',
            ]);
        }

        $adminId = $request->user()?->id;
        $hasViewedPayment = BookingAudit::where('booking_id', $booking->id)
            ->where('actor_user_id', $adminId)
            ->where('action', 'payment_proof_viewed')
            ->exists();

        if (! $hasViewedPayment) {
            return back()->withErrors([
                'payment_status' => 'Please view the payment proof before rejecting this payment.',
            ]);
        }

        $data = $request->validate([
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldPaymentStatus = $booking->payment_status;
        $attempts = (int) ($booking->payment_attempts ?? 0);
        $exceededAttempts = $attempts >= 3;

        $booking->update([
            'payment_status' => $exceededAttempts ? 'pay_on_site' : 'rejected',
            'payment_verified_at' => null,
            'payment_verified_by' => $request->user()->id,
            'payment_notes' => $data['payment_notes'] ?? null,
        ]);

        event(new BookingUpdated($booking, 'payment'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'payment_rejected',
            'old_status' => $booking->status,
            'new_status' => $booking->status,
            'notes' => 'Admin rejected online payment proof.',
            'context' => [
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $exceededAttempts ? 'pay_on_site' : 'rejected',
                'payment_attempts' => $attempts,
            ],
        ]);

        $booking->loadMissing(['user', 'room']);
        if ($exceededAttempts) {
            $this->sendBookingEmail(
                $booking,
                'Online Payment Attempts Exceeded',
                'We could not verify your online payments after multiple attempts. Please proceed to the front desk to settle payment and continue your booking.'
            );
            return back()->with('status', 'Payment marked as pay-on-site after 3 rejected attempts.');
        }

        $this->sendBookingEmail(
            $booking,
            'Payment Rejected - Action Required',
            'We could not verify your online payment. Please upload a new payment screenshot or contact the support desk.'
        );

        return back()->with('status', 'Payment marked as rejected.');
    }

    public function cancelBooking(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['cancelled', 'checked_out'], true)) {
            return back()->withErrors([
                'status' => 'This booking cannot be cancelled in its current status.',
            ]);
        }

        if (! in_array($booking->status, ['pending', 'pending_verification', 'confirmed'], true)) {
            return back()->withErrors([
                'status' => 'Only pending or confirmed bookings can be cancelled.',
            ]);
        }

        $oldStatus = $booking->status;
        $booking->update([
            'status' => 'cancelled',
            'checkout_release_state' => null,
            'checkout_release_available_at' => null,
            'checkout_released_at' => null,
            'checkout_release_admin_id' => null,
        ]);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => request()->user()?->id,
            'action' => 'booking_cancelled',
            'old_status' => $oldStatus,
            'new_status' => 'cancelled',
            'notes' => 'Admin cancelled booking.',
        ]);

        $booking->loadMissing(['user', 'room']);
        $this->sendBookingEmail(
            $booking,
            'Booking Cancelled',
            'Your booking is cancelled, contact the support desk for more information.'
        );

        $statusMessage = in_array($oldStatus, ['confirmed', 'checked_in', 'checkout_scheduled'], true)
            ? 'Confirmed booking cancelled.'
            : 'Booking cancelled.';

        return back()->with('status', $statusMessage);
    }

    public function releaseCheckoutNow(Request $request, Booking $booking): RedirectResponse
    {
        if (! in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true)) {
            return back()->withErrors([
                'status' => 'Only confirmed or checked-in bookings can be released from the checkout queue.',
            ]);
        }

        $oldStatus = $booking->status;
        $booking->update([
            'status' => 'checked_out',
            'checkout_release_state' => 'released_now',
            'checkout_release_available_at' => now(),
            'checkout_released_at' => now(),
            'checkout_release_admin_id' => $request->user()->id,
        ]);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'checkout_released_now',
            'old_status' => $oldStatus,
            'new_status' => 'checked_out',
            'notes' => 'Admin released checkout queue item and marked room available now.',
        ]);

        return back()->with('status', 'Checkout released. Room is now marked available.');
    }

    public function scheduleCheckoutRelease(Request $request, Booking $booking): RedirectResponse
    {
        if (! in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true)) {
            return back()->withErrors([
                'status' => 'Only confirmed or checked-in bookings can be scheduled for checkout release.',
            ]);
        }

        $data = $request->validate([
            'checkout_release_available_at' => ['required', 'date'],
        ]);

        $scheduledAt = Carbon::parse($data['checkout_release_available_at']);
        if ($scheduledAt->lt(now()->subMinutes(1))) {
            return back()->withErrors([
                'checkout_release_available_at' => 'Please select a current or future schedule.',
            ]);
        }

        $oldStatus = $booking->status;
        $booking->update([
            'status' => 'checkout_scheduled',
            'checkout_release_state' => 'scheduled',
            'checkout_release_available_at' => $scheduledAt,
            'checkout_released_at' => null,
            'checkout_release_admin_id' => $request->user()->id,
        ]);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'checkout_release_scheduled',
            'old_status' => $oldStatus,
            'new_status' => 'checkout_scheduled',
            'notes' => 'Admin scheduled room release from checkout queue.',
            'context' => [
                'scheduled_for' => $scheduledAt->toDateTimeString(),
            ],
        ]);

        return back()->with('status', 'Checkout release schedule saved.');
    }

    private function sendBookingEmail(Booking $booking, string $subjectLine, string $messageLine): void
    {
        try {
            Mail::to($booking->user->email)->send(new BookingStatusMail($booking, $subjectLine, $messageLine));
        } catch (\Throwable $exception) {
            Log::warning('Admin-triggered booking email send failed.', [
                'booking_id' => $booking->id,
                'email' => $booking->user->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function updateInventory(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'inventory_date' => ['required', 'date', 'after_or_equal:today'],
            'inventory_end_date' => ['nullable', 'date', 'after_or_equal:inventory_date'],
            'inventory' => ['array'],
            'inventory.*' => ['array'],
            'inventory.*.*' => ['nullable', 'integer', 'min:0', 'max:999'],
            'inventory_additive' => ['nullable', 'boolean'],
        ]);

        $bookableFloorIds = Floor::whereIn('number', [15, 16])->pluck('id')->map(fn ($id) => (int) $id)->all();
        $inventoryRooms = Room::whereIn('slug', ['superior-king', 'junior-suite'])->get();
        if ($inventoryRooms->isEmpty()) {
            $inventoryRooms = Room::where(function ($query) {
                $query->where('name', 'like', '%superior%')
                    ->orWhere('name', 'like', '%junior%');
            })->get();
        }
        $roomLimitsById = [];
        foreach ($inventoryRooms as $room) {
            if ($room->slug === 'superior-king' || str_contains(strtolower($room->name), 'superior')) {
                $roomLimitsById[(int) $room->id] = 11;
                continue;
            }

            if ($room->slug === 'junior-suite' || str_contains(strtolower($room->name), 'junior')) {
                $roomLimitsById[(int) $room->id] = 4;
                continue;
            }
        }

        if (empty($bookableFloorIds) || empty($roomLimitsById)) {
            return back()->withErrors([
                'inventory' => 'Inventory rules are not configured. Ensure floors 15/16 and room types are available.',
            ]);
        }

        $startDate = Carbon::parse($data['inventory_date'])->startOfDay();
        $endDateValue = $data['inventory_end_date'] ?? $data['inventory_date'];
        $endDate = Carbon::parse($endDateValue)->startOfDay();

        if ($endDate->lt($startDate)) {
            return back()->withErrors([
                'inventory_end_date' => 'End date must be the same as or after the start date.',
            ]);
        }

        if ($startDate->diffInDays($endDate) > 90) {
            return back()->withErrors([
                'inventory_end_date' => 'Date range cannot exceed 90 days.',
            ]);
        }

        $dateKeys = [];
        for ($cursor = $startDate->copy(); $cursor->lte($endDate); $cursor->addDay()) {
            $dateKeys[] = $cursor->toDateString();
        }

        $floorTotalLimit = 15;
        $isAdditive = $request->boolean('inventory_additive');

        DB::beginTransaction();
        try {
            foreach ($dateKeys as $dateKey) {
                $existingInventory = DailyRoomInventory::where('inventory_date', $dateKey)
                    ->whereIn('floor_id', $bookableFloorIds)
                    ->whereIn('room_id', array_keys($roomLimitsById))
                    ->get()
                    ->keyBy(fn ($item) => $item->floor_id.'|'.$item->room_id);

                foreach ($data['inventory'] ?? [] as $floorId => $rooms) {
                    $floorId = (int) $floorId;
                    if (! in_array($floorId, $bookableFloorIds, true)) {
                        throw ValidationException::withMessages([
                            'inventory' => 'Only floors 15 and 16 can be configured for room availability.',
                        ]);
                    }

                    $requestedByRoom = [];
                    $floorTotal = 0;
                    foreach ($roomLimitsById as $roomId => $roomLimit) {
                        $value = (int) ($rooms[$roomId] ?? 0);
                        $existing = (int) ($existingInventory->get($floorId.'|'.$roomId)?->available_rooms ?? 0);
                        $totalRooms = $isAdditive ? $existing + $value : $value;

                        if ($totalRooms > $roomLimit) {
                            throw ValidationException::withMessages([
                                'inventory' => 'Per-floor limit exceeded. Superior max is 11 and Junior max is 4.',
                            ]);
                        }
                        $requestedByRoom[$roomId] = $totalRooms;
                        $floorTotal += $totalRooms;
                    }

                    if ($floorTotal > $floorTotalLimit) {
                        throw ValidationException::withMessages([
                            'inventory' => 'Total rooms per floor cannot exceed 15 (4 Superior + 11 Junior).',
                        ]);
                    }

                    foreach ($requestedByRoom as $roomId => $totalRooms) {
                        DailyRoomInventory::updateOrCreate(
                            [
                                'floor_id' => $floorId,
                                'room_id' => $roomId,
                                'inventory_date' => $dateKey,
                            ],
                            ['available_rooms' => (int) ($totalRooms ?? 0)]
                        );
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        event(new InventoryUpdated($startDate->toDateString()));

        $rangeLabel = $startDate->equalTo($endDate)
            ? $startDate->toDateString()
            : $startDate->toDateString().' to '.$endDate->toDateString();
        $successMessage = 'Floor inventory updated for '.$rangeLabel.'.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $successMessage,
                'inventory_date' => $startDate->toDateString(),
                'inventory_end_date' => $endDate->toDateString(),
            ]);
        }

        return redirect()
            ->route('admin.operations', [
                'inventory_date' => $startDate->toDateString(),
                'open_inventory' => 1,
            ])
            ->with('status', $successMessage);
    }

    public function updateRooms(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rooms' => ['required', 'array'],
            'rooms.*.name' => ['required', 'string', 'max:255'],
            'rooms.*.base_rate' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'rooms.*.is_active' => ['nullable', 'in:0,1'],
        ]);

        $roomIds = Room::pluck('id')->all();
        foreach ($data['rooms'] as $roomId => $roomPayload) {
            if (! in_array((int) $roomId, $roomIds, true)) {
                continue;
            }

            Room::whereKey($roomId)->update([
                'name' => trim((string) $roomPayload['name']),
                'base_rate' => isset($roomPayload['base_rate']) && $roomPayload['base_rate'] !== ''
                    ? (float) $roomPayload['base_rate']
                    : null,
                'is_active' => (int) ($roomPayload['is_active'] ?? 0) === 1,
            ]);
        }

        event(new AdminOpsUpdated('room_settings'));

        return back()->with('status', 'Room settings updated successfully.');
    }
}

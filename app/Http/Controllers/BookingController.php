<?php

namespace App\Http\Controllers;

use App\Events\BookingUpdated;
use App\Mail\BookingStatusMail;
use App\Models\Booking;
use App\Models\BookingAudit;
use App\Models\DailyRoomInventory;
use App\Models\Floor;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function confirmations(Request $request): JsonResponse
    {
        $confirmedBookings = $request->user()
            ->bookings()
            ->where('status', 'confirmed')
            ->latest('updated_at')
            ->limit(50)
            ->get(['id', 'payment_status']);

        $cancelledBookingIds = $request->user()
            ->bookings()
            ->where('status', 'cancelled')
            ->latest('updated_at')
            ->limit(50)
            ->pluck('id')
            ->values();

        return response()->json([
            'confirmed_booking_ids' => $confirmedBookings->pluck('id')->values(),
            'confirmed_bookings' => $confirmedBookings
                ->map(fn ($booking) => [
                    'id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                ])
                ->values(),
            'cancelled_booking_ids' => $cancelledBookingIds,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Booking::where('status', 'checkout_scheduled')
            ->whereNotNull('checkout_release_available_at')
            ->where('checkout_release_available_at', '<=', now())
            ->update([
                'status' => 'checked_out',
                'checkout_release_state' => 'released_now',
                'checkout_released_at' => now(),
            ]);

        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'adults' => ['required', 'integer', 'min:1', 'max:10'],
            'children' => ['required', 'integer', 'min:0', 'max:10'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:10'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after_or_equal:check_in_date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i'],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:5120'],
            'id_documents' => ['nullable', 'array', 'min:1', 'max:6'],
            'id_documents.*' => ['file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:5120'],
        ]);

        $room = Room::where('is_active', true)
            ->whereIn('slug', ['junior-suite', 'superior-king'])
            ->find($data['room_id']);

        if (! $room) {
            return back()->withErrors([
                'room_id' => 'Selected room is not available for booking.',
            ])->withInput();
        }

        $checkInDate = Carbon::parse($data['check_in_date'])->startOfDay();
        $checkOutDate = Carbon::parse($data['check_out_date'])->startOfDay();
        $maxCheckoutDate = $checkInDate->copy()->addDays(90);
        if ($checkOutDate->gt($maxCheckoutDate)) {
            return back()->withErrors([
                'check_out_date' => 'Checkout date can be at most 90 days after check-in.',
            ])->withInput();
        }

        foreach ([15, 16] as $floorNumber) {
            Floor::firstOrCreate(
                ['number' => $floorNumber],
                ['label' => 'Floor '.$floorNumber]
            );
        }
        $bookableFloorIds = Floor::whereIn('number', [15, 16])->pluck('id');

        if ($bookableFloorIds->isEmpty()) {
            return back()->withErrors([
                'room_id' => 'Room inventory floors are not configured. Please contact admin.',
            ])->withInput();
        }

        $inventoryRowsOnCheckIn = DailyRoomInventory::where('room_id', $room->id)
            ->whereIn('floor_id', $bookableFloorIds)
            ->whereDate('inventory_date', $checkInDate->toDateString())
            ->get(['available_rooms', 'updated_at']);

        $capacityOnCheckIn = (int) $inventoryRowsOnCheckIn->sum('available_rooms');
        $latestInventoryUpdateOnCheckIn = $inventoryRowsOnCheckIn->max('updated_at');
        $latestInventoryUpdateOnCheckInTs = $latestInventoryUpdateOnCheckIn
            ? Carbon::parse($latestInventoryUpdateOnCheckIn)->getTimestamp()
            : null;

        $overlappingBookings = Booking::where('room_id', $room->id)
            // Cancelled bookings remain blocked for their originally booked stay dates.
            ->whereIn('status', ['pending', 'pending_verification', 'confirmed', 'checked_in', 'checkout_scheduled', 'cancelled'])
            ->whereDate('check_in_date', '<=', $checkInDate->toDateString())
            ->get();

        $bookedOnCheckIn = 0;
        foreach ($overlappingBookings as $booking) {
            $bookingCheckIn = Carbon::parse($booking->check_in_date)->startOfDay();
            $bookingCheckOut = Carbon::parse($booking->check_out_date)->startOfDay();

            $defaultEnd = $bookingCheckOut->copy()->subDay();
            if ($bookingCheckOut->lessThanOrEqualTo($bookingCheckIn)) {
                $defaultEnd = $bookingCheckIn->copy();
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
                    $blockEnd = $bookingCheckOut->copy();
                }
            }

            if ($blockEnd->lt($bookingCheckIn)) {
                continue;
            }

            if ($checkInDate->gte($bookingCheckIn) && $checkInDate->lte($blockEnd)) {
                if ($booking->status === 'cancelled') {
                    $cancelledAtTs = optional($booking->updated_at)->getTimestamp();
                    if ($cancelledAtTs && $latestInventoryUpdateOnCheckInTs && $latestInventoryUpdateOnCheckInTs >= $cancelledAtTs) {
                        // Admin manually re-opened this check-in date after cancellation.
                        continue;
                    }
                }
                $bookedOnCheckIn += (int) $booking->rooms_count;
            }
        }

        if ($capacityOnCheckIn <= 0) {
            return back()->withErrors([
                'room_id' => 'This room cannot be booked yet. Admin has not set room inventory on the selected check-in date.',
            ])->withInput();
        }

        if (($bookedOnCheckIn + (int) $data['rooms_count']) > $capacityOnCheckIn) {
            return back()->withErrors([
                'rooms_count' => 'Requested number of rooms exceeds available inventory on the selected check-in date.',
            ])->withInput();
        }

        $uploadedDocuments = $request->file('id_documents', []);

        if (empty($uploadedDocuments) && $request->hasFile('id_document')) {
            $uploadedDocuments = [$request->file('id_document')];
        }

        if (empty($uploadedDocuments)) {
            return back()->withErrors([
                'id_documents' => 'Please upload at least one valid ID image.',
            ])->withInput();
        }

        if (count($uploadedDocuments) > 6) {
            return back()->withErrors([
                'id_documents' => 'You can upload a maximum of 6 ID images.',
            ])->withInput();
        }

        $idDocumentPaths = collect($uploadedDocuments)
            ->map(fn ($file) => $file->store('booking-ids', 'private'))
            ->values()
            ->all();

        $idDocumentPath = $idDocumentPaths[0] ?? null;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $data['room_id'],
            'adults' => $data['adults'],
            'children' => $data['children'],
            'rooms_count' => $data['rooms_count'],
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'check_in_time' => $data['check_in_time'],
            'check_out_time' => $data['check_out_time'],
            'status' => 'pending_verification',
            'id_document_path' => $idDocumentPath,
            'id_document_paths' => $idDocumentPaths,
            'id_document_uploaded_at' => now(),
        ]);

        $booking->load(['user', 'room']);

        event(new BookingUpdated($booking, 'booking'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'booking_submitted',
            'old_status' => null,
            'new_status' => $booking->status,
            'notes' => 'Customer submitted booking with valid ID document.',
            'context' => [
                'adults' => $booking->adults,
                'children' => $booking->children,
                'rooms_count' => $booking->rooms_count,
                'check_in_date' => $booking->check_in_date?->toDateString(),
            ],
        ]);

        $this->sendBookingEmail(
            $booking,
            'Booking Submitted - Pending Verification',
            'We received your booking request and valid ID. Our admin team will verify your document before confirmation.'
        );

        return back()
            ->with('status', 'Booking submitted with ID. Awaiting admin verification and confirmation.')
            ->with('submitted_booking_id', $booking->id);
    }

    public function showPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($booking->payment_status === 'pay_on_site') {
            return redirect()
                ->route('rooms.history')
                ->withErrors(['payment_proof' => 'Online payment is no longer available for this booking. Please proceed to the front desk.']);
        }

        if (! in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true)) {
            return redirect()
                ->route('rooms.history')
                ->withErrors(['payment_proof' => 'Payment is only available for confirmed bookings.']);
        }

        $booking->loadMissing('room');

        return view('rooms.payment', compact('booking'));
    }

    public function submitPaymentProof(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true)) {
            return back()->withErrors([
                'payment_proof' => 'Payment proof can only be uploaded for confirmed bookings.',
            ]);
        }

        if ($booking->payment_status === 'verified') {
            return back()->withErrors([
                'payment_proof' => 'Payment is already verified for this booking.',
            ]);
        }

        if ($booking->payment_status === 'pay_on_site') {
            return back()->withErrors([
                'payment_proof' => 'Online payment is no longer available for this booking. Please proceed to the front desk.',
            ]);
        }

        if ($booking->payment_status === 'submitted') {
            return back()->withErrors([
                'payment_proof' => 'Payment proof is already submitted and awaiting confirmation.',
            ]);
        }

        $booking->loadMissing('room');

        $attempts = (int) ($booking->payment_attempts ?? 0);
        if ($attempts >= 3) {
            return back()->withErrors([
                'payment_proof' => 'Online payment attempts exceeded. Please proceed to the front desk for payment.',
            ]);
        }

        $data = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_reference' => ['required', 'string', 'max:100'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $checkIn = $booking->check_in_date ? Carbon::parse($booking->check_in_date) : null;
        $checkOut = $booking->check_out_date ? Carbon::parse($booking->check_out_date) : null;
        $stayNights = 1;
        if ($checkIn && $checkOut) {
            $stayNights = max(1, $checkIn->diffInDays($checkOut));
        }

        $baseRate = (float) ($booking->room?->base_rate ?? 0);
        $roomsCount = (int) ($booking->rooms_count ?? 1);
        $totalAmount = round($baseRate * $stayNights * $roomsCount, 2);
        $downpaymentAmount = round($totalAmount * 0.15, 2);

        if ($totalAmount <= 0) {
            return back()->withErrors([
                'payment_amount' => 'Payment amount cannot be calculated for this booking. Please contact support.',
            ])->withInput();
        }

        $submittedAmount = round((float) $data['payment_amount'], 2);
        $matchesDownpayment = abs($submittedAmount - $downpaymentAmount) < 0.01;
        $matchesTotal = abs($submittedAmount - $totalAmount) < 0.01;

        if (! ($matchesDownpayment || $matchesTotal)) {
            return back()->withErrors([
                'payment_amount' => 'Payment amount must match the 15% downpayment (PHP '.number_format($downpaymentAmount, 2).') or the full total (PHP '.number_format($totalAmount, 2).').',
            ])->withInput();
        }

        if (!empty($booking->payment_proof_path) && Storage::disk('private')->exists($booking->payment_proof_path)) {
            Storage::disk('private')->delete($booking->payment_proof_path);
        }

        $proofPath = $data['payment_proof']->store('payment-proofs', 'private');
        $oldPaymentStatus = $booking->payment_status;

        $booking->update([
            'payment_status' => 'submitted',
            'payment_proof_path' => $proofPath,
            'payment_submitted_at' => now(),
            'payment_verified_at' => null,
            'payment_verified_by' => null,
            'payment_notes' => null,
            'payment_amount' => $submittedAmount,
            'payment_reference' => trim($data['payment_reference']),
            'payment_attempts' => $attempts + 1,
        ]);

        event(new BookingUpdated($booking, 'payment'));

        BookingAudit::create([
            'booking_id' => $booking->id,
            'actor_user_id' => $request->user()->id,
            'action' => 'payment_submitted',
            'old_status' => $booking->status,
            'new_status' => $booking->status,
            'notes' => 'Customer uploaded online payment proof.',
            'context' => [
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => 'submitted',
                'payment_amount' => $submittedAmount,
                'payment_reference' => trim($data['payment_reference']),
                'payment_attempts' => $attempts + 1,
            ],
        ]);

        return back()->with('payment_status', 'Payment proof submitted. Awaiting confirmation.');
    }

    private function sendBookingEmail(Booking $booking, string $subjectLine, string $messageLine): void
    {
        try {
            Mail::to($booking->user->email)->send(new BookingStatusMail($booking, $subjectLine, $messageLine));
        } catch (\Throwable $exception) {
            Log::warning('Booking email send failed.', [
                'booking_id' => $booking->id,
                'email' => $booking->user->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}

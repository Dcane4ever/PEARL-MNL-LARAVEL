<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Booking $booking,
        public ?string $scope = 'booking'
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.operations'),
            new PrivateChannel('user.'.$this->booking->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'scope' => $this->scope,
            'booking_id' => $this->booking->id,
            'user_id' => $this->booking->user_id,
            'status' => $this->booking->status,
            'payment_status' => $this->booking->payment_status,
            'updated_at' => optional($this->booking->updated_at)->toIso8601String(),
        ];
    }
}

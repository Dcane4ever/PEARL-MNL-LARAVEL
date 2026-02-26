<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminOpsUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $scope = 'admin'
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.operations'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'scope' => $this->scope,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}

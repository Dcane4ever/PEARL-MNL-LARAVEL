<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $inventoryDate
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.operations'),
            new Channel('inventory'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'inventory.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'scope' => 'inventory',
            'inventory_date' => $this->inventoryDate,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}

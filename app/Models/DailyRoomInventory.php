<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRoomInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'room_id',
        'inventory_date',
        'available_rooms',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}

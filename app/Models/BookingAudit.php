<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'actor_user_id',
        'action',
        'old_status',
        'new_status',
        'notes',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}

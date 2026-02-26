<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'adults',
        'children',
        'rooms_count',
        'check_in_date',
        'check_out_date',
        'check_in_time',
        'check_out_time',
        'status',
        'payment_status',
        'payment_proof_path',
        'payment_submitted_at',
        'payment_verified_at',
        'payment_verified_by',
        'payment_notes',
        'payment_amount',
        'payment_reference',
        'payment_attempts',
        'checkout_release_state',
        'checkout_release_available_at',
        'checkout_released_at',
        'checkout_release_admin_id',
        'id_document_path',
        'id_document_paths',
        'id_document_uploaded_at',
        'verified_by_admin_id',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'id_document_paths' => 'array',
        'id_document_uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'checkout_release_available_at' => 'datetime',
        'checkout_released_at' => 'datetime',
        'payment_submitted_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'payment_amount' => 'decimal:2',
        'payment_attempts' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function verifiedByAdmin()
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    public function checkoutReleasedByAdmin()
    {
        return $this->belongsTo(User::class, 'checkout_release_admin_id');
    }

    public function paymentVerifiedByAdmin()
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function audits()
    {
        return $this->hasMany(BookingAudit::class);
    }
}

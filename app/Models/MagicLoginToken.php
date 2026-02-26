<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagicLoginToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_hash',
        'requested_ip',
        'requested_user_agent',
        'consumed_ip',
        'consumed_user_agent',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

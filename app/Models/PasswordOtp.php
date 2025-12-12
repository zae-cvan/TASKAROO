<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordOtp extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'otp', 'expires_at'];

    // Cast expires_at as datetime
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired()
    {
        // Ensure expires_at is set and compare with now
        if (!$this->expires_at) {
            return true;
        }
        return $this->expires_at->lt(Carbon::now());
    }
}

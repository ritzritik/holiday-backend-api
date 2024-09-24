<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_booking_id',
        'voucher_code',
        'description',
        'amount',
        'currency',
        'expiry_date',
        'terms_and_conditions',
        'status',
    ];

    // A voucher belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A voucher belongs to a booking
    public function booking()
    {
        return $this->belongsTo(UserBooking::class);
    }
}

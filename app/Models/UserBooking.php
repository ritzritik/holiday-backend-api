<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_reference',
        'booking_type',
        'supplier_name',
        'supplier_code',
        'booking_details',
        'price',
        'currency',
        'check_in_date',
        'check_out_date',
        'stay_duration',
        'room_type',
        'board_basis',
        'star_rating',
        'booking_status',
        'additional_information',
    ];

    // A booking belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A booking can have many vouchers
    public function vouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }
}

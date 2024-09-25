<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerDetails extends Model
{
    use HasFactory;

    protected $table = 'passenger_details';

    protected $fillable = [
        'booking_id', 'title', 'first_name', 'surname', 'email', 'payment_status', 'contact_number', 'package_type', 'price', 'user_id',
    ];

    public function booking()
    {
        return $this->belongsTo(BookingDetails::class, 'booking_id', 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

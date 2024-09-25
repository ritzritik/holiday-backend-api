<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardDetails extends Model
{
    use HasFactory;

    protected $table = 'card_details';

    protected $fillable = [
        'booking_id', 'user_id', 'card_number', 'card_holder_name', 'expiry_date', 'cvv', 'billing_address',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetails extends Model
{
    use HasFactory;

    protected $table = 'booking_details';

    protected $fillable = [
        'booking_id', 
        'refnum', 
        'package_details',
    ];

    public function passengers()
    {
        return $this->hasMany(PassengerDetails::class, 'booking_id', 'booking_id');
    }

    public function cardDetails()
    {
        return $this->hasOne(CardDetails::class, 'booking_id', 'booking_id');
    }
}

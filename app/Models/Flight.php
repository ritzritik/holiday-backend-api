<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'departure',
        'arrival',
        'status',
        'departure_time',
        'arrival_time',
        'duration',
        'price',
        'stops',
        'is_deleted',
        'created_by',
        'updated_by'
    ];

    //    public static function find(mixed $flightId)
    //    {
    //        return Flights::query()->where('id', $flightId)->first();
    //    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

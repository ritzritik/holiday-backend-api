<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirportPricing extends Model
{
    use HasFactory;

    protected $table = 'airport_pricing';

    protected $fillable = ['airport_id', 'private_parking_price', 'standard_parking_price'];

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirportPricing extends Model
{
    use HasFactory;

    protected $table = 'airport_pricing';

    protected $fillable = ['airport_id', 'transfer_price', 'parking_price'];

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }
}

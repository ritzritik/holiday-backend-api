<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkiResort extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'ski_resort_api_id', 'ski_countries_id'];

    public function country()
    {
        return $this->belongsTo(SkiCountry::class);
    }
}

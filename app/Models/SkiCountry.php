<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkiCountry extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'ski_country_api_id'];

    public function skiresorts()
    {
        return $this->hasMany(SkiResort::class);
    }
}

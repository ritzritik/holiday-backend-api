<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Airport extends Model
{
    // use HasFactory;
    // protected $fillable = ['id', 'name'];

    // public static function fetchFromApi()
    // {
    //     $response = Http::get('https://api.example.com/destinations');
    //     if ($response->successful()) {
    //         return collect($response->json())->map(function ($item) {
    //             return [
    //                 'id' => $item['id'],
    //                 'name' => $item['name'],
    //             ];
    //         });
    //     }
    //     return collect([]);
    // }
    protected $table = 'airports';

    protected $fillable = ['name', 'code', 'region'];

    public function pricing()
    {
        return $this->hasMany(AirportPricing::class);
    }
}

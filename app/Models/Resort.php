<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Resort extends Model
{
    // use HasFactory;
    protected $fillable = ['name', 'resort_api_id', 'area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

//    public static function fetchFromApi()
//    {
//        $response = Http::get('https://api.example.com/destinations');
//        if ($response->successful()) {
//            return collect($response->json())->map(function ($item) {
//                return [
//                    'id' => $item['id'],
//                    'name' => $item['name'],
//                ];
//            });
//        }
//        return collect([]);
//    }
}

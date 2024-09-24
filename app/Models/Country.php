<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Country extends Model
{
    protected $fillable = ['name', 'country_api_id'];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public static function fetchFromApi()
    {
        $apiUrl = env('API_URL');
        if (!$apiUrl) {
            throw new \Exception('API_URL not defined in the .env file');
        }

        $response = Http::get("{$apiUrl}?agtid=144&page=country");

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $json = json_encode($xml);
            $array = json_decode($json, true);

            return collect($array['Country'])->map(function ($item) {
                return [
                    'id' => $item['@attributes']['Id'],
                    'name' => $item['@attributes']['Name'],
                ];
            });
        }

        return collect([]);
    }
}



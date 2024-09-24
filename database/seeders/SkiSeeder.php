<?php

namespace Database\Seeders;

use App\Models\SkiCountry;
use App\Models\SkiResort;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SkiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Delete existing records to avoid conflicts
        DB::transaction(function () {
            DB::table('ski_resorts')->delete();
            DB::table('ski_countries')->delete();
        });

        $apiUrl = env('API_URL');
        $response = Http::get("{$apiUrl}?agtid=144&page=skidest");

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $json = json_encode($xml);
            $countries = json_decode($json, true)['Country'] ?? [];

            // Handle single Country object
            if (isset($countries['@attributes'])) {
                $countries = [$countries];
            }

            foreach ($countries as $countryData) {
                $country = SkiCountry::create([
                    'ski_country_api_id' => $countryData['@attributes']['Id'] ?? null,
                    'name' => $countryData['@attributes']['Name'] ?? '',
                ]);

                $resorts = $countryData['Resort'] ?? [];

                // Handle single Resort object
                if (isset($resorts['@attributes'])) {
                    $resorts = [$resorts];
                }

                foreach ($resorts as $resortData) {
                    SkiResort::create([
                        'ski_resort_api_id' => $resortData['@attributes']['Id'] ?? null,
                        'ski_countries_id' => $country->id,
                        'name' => $resortData['@attributes']['Name'] ?? '',
                    ]);
                }
            }
        }
    }
}

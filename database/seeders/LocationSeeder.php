<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Region;
use App\Models\Area;
use App\Models\Resort;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        // Delete existing records to avoid conflicts
        DB::transaction(function () {
            DB::table('resorts')->delete();
            DB::table('areas')->delete();
            DB::table('regions')->delete();
            DB::table('countries')->delete();
        });

        $apiUrl = env('API_URL');
        $response = Http::get("{$apiUrl}?agtid=144&page=country");

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $json = json_encode($xml);
            $countries = json_decode($json, true)['Country'];

            foreach ($countries as $countryData) {
                $country = Country::create([
                    'country_api_id' => $countryData['@attributes']['Id'] ?? null,
                    'name' => $countryData['@attributes']['Name'] ?? '',
                ]);

                $regionsResponse = Http::get("{$apiUrl}?agtid=144&page=resort&countryid={$country->country_api_id}");
                if ($regionsResponse->successful()) {
                    $regionsXml = simplexml_load_string($regionsResponse->body());
                    $regionsJson = json_encode($regionsXml);
                    $regions = json_decode($regionsJson, true)['Country']['Region'] ?? [];

                    // Handle single Region objects
                    if (isset($regions['@attributes'])) {
                        $regions = [$regions];
                    }

                    foreach ($regions as $regionData) {
                        $region = $country->regions()->create([
                            'region_api_id' => $regionData['@attributes']['Id'] ?? null,
                            'name' => $regionData['@attributes']['Name'] ?? '',
                        ]);

                        $areas = $regionData['Area'] ?? [];

                        // Handle single Area objects
                        if (isset($areas['@attributes'])) {
                            $areas = [$areas];
                        }

                        foreach ($areas as $areaData) {
                            $area = $region->areas()->create([
                                'area_api_id' => $areaData['@attributes']['Id'] ?? null,
                                'name' => $areaData['@attributes']['Name'] ?? '',
                            ]);

                            $resorts = $areaData['Resort'] ?? [];

                            // Handle single Resort objects
                            if (isset($resorts['@attributes'])) {
                                $resorts = [$resorts];
                            }

                            foreach ($resorts as $resortData) {
                                $area->resorts()->create([
                                    'resort_api_id' => $resortData['@attributes']['Id'] ?? null,
                                    'name' => $resortData['@attributes']['Name'] ?? '',
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}

<?php

namespace App\Http\Controllers;


use App\Models\Area;
use App\Models\Region;
use App\Models\SkiCountry;
use App\Models\SkiResort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Destination;
use App\Models\PackageType;
use App\Models\Country;
use App\Models\Airport;
use App\Models\Hotel;
use App\Models\NewsLetter;
use App\Models\Resort;
use Illuminate\Support\Facades\Cache;


class HomeController extends Controller
{
    public function fetchApiData($page, $params): array
    {
        $apiUrl = 'http://87.102.127.86:8119/search/searchoffers.dll';
        $params['agtid'] = '100';
        $params['page'] = $page;
        $params['platform'] = 'WEB';
        $params['output'] = 'JSON';

        $queryString = http_build_query($params);
        $response = @file_get_contents("{$apiUrl}?{$queryString}");

        if ($response === false) {
            throw new \Exception("API request failed");
        }

        $response = trim($response);
        $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
        }

        return $data['Offers'] ?? [];
    }

    public function index(): JsonResponse
    {
        $startTime = microtime(true);
    
        $featuredDeals = Coupon::where([
            ['active', 1],
            ['is_deleted', 0],
            ['is_expired', 0]
        ])->get();
    
        $countries = Cache::remember('countries', 60 * 60 * 12, function () {
            return Country::all()->pluck('name', 'country_api_id')
                ->map(function ($name, $id) {
                    return ['id' => $id, 'name' => $name];
                })->values();
        });
    
        $hotelCountriesMapped = Cache::remember('hotel_countries', 60 * 60 * 12, function () {
            return Country::all(['id', 'country_api_id', 'name'])
                ->map(function ($hotelCountry) {
                    return [
                        'id' => (integer)$hotelCountry->id,
                        'api_id' => (integer)$hotelCountry->country_api_id,
                        'name' => $hotelCountry->name,
                    ];
                });
        });
    
        $hotelRegionsMapped = Cache::remember('hotel_regions', 60 * 60 * 12, function () {
            return Region::all(['id', 'region_api_id', 'name', 'country_id'])
                ->map(function ($hotelRegion) {
                    return [
                        'id' => (integer)$hotelRegion->region_api_id,
                        'name' => $hotelRegion->name,
                        'country_id' => (integer)$hotelRegion->country_id,
                    ];
                })->values();
        });
    
        $hotelAreasMapped = Cache::remember('hotel_areas', 60 * 60 * 12, function () {
            return Area::all(['id', 'area_api_id', 'name', 'region_id'])
                ->map(function ($hotelArea) {
                    return [
                        'id' => (integer)$hotelArea->area_api_id,
                        'name' => $hotelArea->name,
                        'region_id' => (integer)$hotelArea->region_id,
                    ];
                })->values();
        });
    
        $hotelResortsMapped = Cache::remember('hotel_resorts', 60 * 60 * 12, function () use ($hotelCountriesMapped) {
            return Resort::with('area.region.country')
                ->get(['resort_api_id', 'name', 'area_id'])
                ->map(function ($hotelResort) use ($hotelCountriesMapped) {
                    $area = $hotelResort->area;
                    $region = $area->region;
                    $country = $region->country;
    
                    return [
                        'id' => (integer)$hotelResort->resort_api_id,
                        'name' => $hotelResort->name,
                        'region_id' => (integer)$region->region_api_id,
                        'country_api_id' => (integer)$country->country_api_id,
                        'area_id' => $hotelResort->area_id ?? null,
                    ];
                })->values();
        });
    
        $skiCountriesMapped = Cache::remember('ski_countries', 60 * 60 * 12, function () {
            return SkiCountry::all(['id', 'ski_country_api_id', 'name'])
                ->map(function ($skiCountry) {
                    return [
                        'id' => (integer)$skiCountry->id,
                        'api_id' => (integer)$skiCountry->ski_country_api_id,
                        'name' => $skiCountry->name,
                    ];
                });
        });
    
        $skiResortsMapped = Cache::remember('ski_resorts', 60 * 60 * 12, function () use ($skiCountriesMapped) {
            return SkiResort::all(['ski_resort_api_id', 'name', 'ski_countries_id'])
                ->map(function ($skiResort) use ($skiCountriesMapped) {
                    $countryApiId = $skiCountriesMapped->firstWhere('id', $skiResort->ski_countries_id)['api_id'];
                    return [
                        'id' => (integer)$skiResort->ski_resort_api_id,
                        'name' => $skiResort->name,
                        'ski_country_api_id' => $countryApiId,
                    ];
                })->values();
        });
    
        $hotelsArray = Cache::remember('hotels_search', 60 * 30, function () {
            $hotelParams = [
                'countryid' => 1,
                'regionid' => 0,
                'areaid' =>  0,
                'resortid' => 0,
                'depdate' => date('d/m/Y'),
                'adults' => 2,
                'children' => 0,
                'duration' => 7,
                'hotelid' => 0,
            ];
            return $this->fetchApiData('HTLSEARCH', $hotelParams);
        });
    
        $recentBookedHotels = [];
        foreach ($hotelsArray as $hotel) {
            $htlName = explode(' ', $hotel['htlname'])[0];
            if (!isset($recentBookedHotels[$htlName])) {
                $recentBookedHotels[$htlName] = $hotel;
            }
            if (count($recentBookedHotels) === 4) {
                break;
            }
        }
    
        $recentBookedHotels = array_values($recentBookedHotels);
    
        $packagesArray = Cache::remember('holiday_packages', 60 * 30, function () {
            $packageParams = [
                'depart' => 'LGW|STN|LHR|LCY|SEN|LTN',
                'countryid' => '1',
                'regionid' => '4',
                'areaid' => '9',
                'resortid' => '0',
                'depdate' => date('d/m/Y'),
                'flex' => '3',
                'adults' => '2',
                'children' => '0',
                'duration' => '7',
                'minprice' => '0.00',
                'maxprice' => '0.00',
                'type' => 'DPCP',
            ];
            return $this->fetchApiData('HOLSEARCH', $packageParams);
        });
    
        $payDeals = array_slice($packagesArray, 0, 9);
    
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $airportData = include(base_path('config/airports.php'));
        return response()->json([
            'data' => [
                'airports' => $airportData,
                'featuredDeals' => $featuredDeals,
                'countries' => $countries,
                'hotelCountries' => $hotelCountriesMapped->values(),
                'hotelRegions' => $hotelRegionsMapped,
                'hotelAreas' => $hotelAreasMapped,
                'hotelResorts' => $hotelResortsMapped,
                'skiCountries' => $skiCountriesMapped->values(),
                'skiResorts' => $skiResortsMapped,
                'recentBookedHotels' => $recentBookedHotels,
                'payDeals' => $payDeals,
            ],
            'message' => 'success',
            'statusCode' => 200,
            'executionTime' => $executionTime,
        ]);
    }
    

    public function subscribe(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $email = $request->input('email');
            $newsletter = new NewsLetter();
            $newsletter->email = $email;
    
            try {
                $newsletter->save();
                return response()->json([
                    'data' => [],
                    'message' => 'Subscription successful.',
                    'statusCode' => 200
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'data' => [],
                    'message' => 'Failed to subscribe.',
                    'statusCode' => 500
                ]);
            }
        }
    
        return response()->json([
            'message' => 'Invalid request method.',
            'statusCode' => 400
        ]);
    }
    
}

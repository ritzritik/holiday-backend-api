<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HotelsController extends Controller
{
    public function index(Request $request)
    {
        $apiUrl = env('API_URL');
        $queryParams = [
            'agtid' => '144',
            'page' => 'HTLSEARCH',
            'platform' => 'WEB',
            'countryid' => $request->input('countryid', '1'),
            'regionid' => $request->input('regionid', '4'),
            'areaid' => $request->input('areaid', '9'),
            'resortid' => $request->input('resortid', '0'),
            'depdate' => date('d/m/Y'),
            'flex' => $request->input('flex', '3'),
            'star' => $request->input('star', ''),
            'adults' => $request->input('adults', '2'),
            'children' => $request->input('children', '0'),
            'duration' => $request->input('duration', '7'),
            'hotelid' => $request->input('hotelid', '0'),
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $packagesArray = json_decode($response, true)['Offers'] ?? [];

        // Filter hotels based on input
        $filteredHotels = array_filter($packagesArray, function ($package) use ($request) {
            return ($request->input('minprice') == 0.00 || $package['htlnetpp'] >= $request->input('minprice')) &&
                ($request->input('maxprice') == 0.00 || $package['htlsellpp'] <= $request->input('maxprice')) &&
                ($request->input('star') == '' || $package['starrating'] == $request->input('star')) &&
                ($request->input('duration') == '' || $package['stay'] == $request->input('duration'));
        });

        // Sort by price if specified
        if ($request->input('sort') == 'price') {
            usort($filteredHotels, fn($a, $b) => $a['htlsellpp'] <=> $b['htlsellpp']);
        }

        // Return response as JSON
        return response()->json([
            'hotels' => array_values($filteredHotels),
            'bestSellingHotels' => array_slice($filteredHotels, 0, 10),
        ]);
    }

    public function hotelSearch(Request $request)
    {
        $apiUrl = 'http://87.102.127.86:8119/search/searchoffers.dll';
        $hoteldestination = $request->input('hoteldestination');
        $depdate = $request->input('departureDate');
        $formattedDepdate = \Carbon\Carbon::createFromFormat('Y-m-d', $depdate)->format('d/m/Y');
        $duration = preg_replace('/\D/', '', $request->input('duration', '7'));
        $adults = preg_replace('/\D/', '', $request->input('adults', '2'));

        $queryParams = [
            'agtid' => '144',
            'page' => 'HTLSEARCH',
            'platform' => 'WEB',
            'countryid' => $hoteldestination,
            'regionid' => $request->input('region', 0),
            'areaid' => $request->input('area', 0),
            'resortid' => $request->input('resort', 0),
            'depdate' => $formattedDepdate,
            'flex' => '',
            'star' => '',
            'adults' => $adults,
            'children' => 0,
            'duration' => $duration,
            'hotelid' => 0,
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $hotelsArray = json_decode($response, true)['Offers'] ?? [];

        // Sorting
        $sortOption = $request->input('sort', '');
        if ($sortOption) {
            usort($hotelsArray, function ($a, $b) use ($sortOption) {
                return match ($sortOption) {
                    'price_asc' => ($a['htlsellpp'] ?? 0) <=> ($b['htlsellpp'] ?? 0),
                    'price_desc' => ($b['htlsellpp'] ?? 0) <=> ($a['htlsellpp'] ?? 0),
                    default => 0,
                };
            });
        }

        // Budget filter
        $priceRanges = [
            'under_100' => [0, 100],
            '100_500' => [100, 500],
            '500_1000' => [500, 1000],
            '1000_2000' => [1000, 2000],
            'over_2000' => [2000, PHP_INT_MAX],
        ];
        $budgetFilter = $request->input('budget');
        if (isset($priceRanges[$budgetFilter])) {
            [$minPrice, $maxPrice] = $priceRanges[$budgetFilter];
            $hotelsArray = array_filter($hotelsArray, fn($package) => ($package['htlsellpp'] ?? 0) >= $minPrice && ($package['htlsellpp'] ?? 0) <= $maxPrice);
        }

        // Return JSON response
        return response()->json([
            'message' => 'success',
            'data' => [
                'hotels' => array_values($hotelsArray),
                'queryParams' => $request->query() ?: null,
            ],
            'statusCode' => 200
        ]);
    }

    public function hotel_booking_details(Request $request)
    {
        $hotels = $request['hotel'];
        $apiUrl = env('API_URL');

        $queryParams = [
            'agtid' => '144',
            'page' => 'CALSEARCH',
            'platform' => 'WEB',
            'depart' => 'LGW',
            'Monthyear' => date('m/Y'),
            'adults' => 2,
            'children' => 0,
            'duration' => 7,
            'output' => 'JSON',
            'hotelid' => $hotels['kwikid'],
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $calendarPricesArray = json_decode($response, true)['Offers'] ?? [];

        // Booking info
        $queryParams = [
            'agtid' => '144',
            'page' => 'BROCHURE',
            'brochurecode' => $hotels['brochurecode'],
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $hotelInfoArray = json_decode($response, true)['content'] ?? [];

        return response()->json([
            'selected_hotel' => $hotels,
            'calendarPrices' => $calendarPricesArray,
            'hotelInfo' => $hotelInfoArray,
        ]);
    }

    public function hotel_checkout(Request $request)
    {
        if (!Session::has('expiry_time')) {
            $sessionLifetime = (int) config('session.lifetime', 30);
            $expiryTime = now()->addMinutes($sessionLifetime);
            Session::put('expiry_time', $expiryTime);
        }

        $checkout_hotel = $request->input('selected_hotel');
        $bookingId = strtoupper(Str::random(8));
        $formattedExpiryTime = Session::get('expiry_time')->toIso8601String();

        return response()->json([
            'checkout_hotel' => $checkout_hotel,
            'booking_id' => $bookingId,
            'session_expiry' => $formattedExpiryTime,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class FlightsController extends Controller
{
    public function index(Request $request)
    {
        $apiUrl = env('API_URL');
        $params = [
            'agtid' => 144,
            'page' => 'FLTSEARCH',
            'platform' => 'WEB',
            'depart' => $request->input('origin', 'LGW|STN'),
            'arrive' => $request->input('destination', 'TFS'),
            'depdate' => $request->input('departureDate', date('d/m/Y')),
            'flex' => 3,
            'duration' => 7,
            'output' => 'JSON'
        ];

        $queryParams = http_build_query($params);
        $response = file_get_contents("{$apiUrl}?{$queryParams}");
        $flights = json_decode($response, true)['Offers'] ?? [];

        // Ensure we have an array of flights
        if (!is_array($flights)) {
            $flights = [];
        }

        // Apply sorting
        if ($sortOption = $request->input('sort')) {
            usort($flights, function ($a, $b) use ($sortOption) {
                switch ($sortOption) {
                    case 'price_asc':
                        return $a['fltSellpricepp'] <=> $b['fltSellpricepp'];
                    case 'price_desc':
                        return $b['fltSellpricepp'] <=> $a['fltSellpricepp'];
                    case 'departure_asc':
                        return strtotime($a['outdep']) <=> strtotime($b['outdep']);
                    case 'departure_desc':
                        return strtotime($b['outdep']) <=> strtotime($a['outdep']);
                    default:
                        return 0;
                }
            });
        }

        // Apply budget filtering
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
            $flights = array_filter($flights, function ($flight) use ($minPrice, $maxPrice) {
                return ((float) $flight['fltSellpricepp']) >= $minPrice && ((float) $flight['fltSellpricepp']) <= $maxPrice;
            });
        }

        // Apply stops filtering if applicable
        $stopsFilter = $request->input('stops');
        if ($stopsFilter && $stopsFilter !== 'any') {
            // Adjust this logic based on your actual data structure for stops
            // Example logic (assuming stops data is available in your API response):
            $flights = array_filter($flights, function ($flight) use ($stopsFilter) {
                return $flight['stops'] == (int)$stopsFilter; // Example condition
            });
        }

        // Return flights data as JSON
        return response()->json([
            'flights' => $flights,
            'queryParams' => $request->query() ?: null,
            'success' => session('success'), // If you want to return session messages
        ]);
    }

    public function alternateFlights(Request $request)
    {
        if ($request->isMethod('post')) {
            $flightSearch = $request->input('flightSearch');
            $formattedDepdate = \Carbon\Carbon::createFromFormat('Y-m-d', $flightSearch['departureDate'])->format('d/m/Y');

            $apiUrl = env('API_URL');
            $queryParams = [
                'agtid' => '144',
                'page' => 'FLTSEARCH',
                'platform' => 'WEB',
                'depart' => $flightSearch['origin'],
                'arrive' => $flightSearch['arrivalAirport'],
                'depdate' => $formattedDepdate,
                'flex' => $flightSearch['flex'],
                'duration' => $flightSearch['stay'],
                'output' => 'JSON',
            ];

            $queryString = http_build_query($queryParams);
            $response = file_get_contents("{$apiUrl}?{$queryString}");
            $flights = json_decode($response, true)['Offers'] ?? [];

            // Limit to first 2 flights
            return response()->json(['flights' => array_slice($flights, 0, 2)]);
        }

        return response()->json(['error' => 'Invalid request method.'], 400);
    }

    public function flightSearch(Request $request)
    {
        $apiUrl = env('API_URL');
        $formattedDepdate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('departureDate'))->format('d/m/Y');
        $adults = (int) $request->input('adults', 1);
        $children = (int) $request->input('children', 0);
        $formattedDuration = preg_replace('/\D/', '', $request->input('duration'));

        $queryParams = [
            'agtid' => '144',
            'page' => 'FLTSEARCH',
            'platform' => 'WEB',
            'depart' => $request->input('origin', 'LGW'),
            'arrive' => $request->input('destination', 'PFO'),
            'depdate' => $formattedDepdate,
            'flex' => $request->input('flex', ''),
            'duration' => $formattedDuration,
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $flights = json_decode(trim($response), true)['Offers'] ?? [];

        foreach ($flights as &$flight) {
            $pricePerPerson = (float) ($flight['fltSellpricepp'] ?? 0);
            $totalPrice = $pricePerPerson * ($adults + $children);
            $flight['totalPrice'] = $totalPrice;
            $flight['adults'] = $adults;
            $flight['children'] = $children;
        }

        return response()->json(['flights' => $flights]);
    }

    public function bookingDetails(Request $request)
    {
        $flight = $request->input('flight');
        $depdate = explode(' ', $flight['outdep'])[0];
        $apiUrl = env('API_URL');

        $queryParams = [
            'agtid' => '144',
            'page' => 'FLTSEARCH',
            'platform' => 'WEB',
            'depart' => $flight['depapt'],
            'arrive' => $flight['arrapt'],
            'depdate' => $depdate,
            'flex' => 15,
            'duration' => 15,
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $calendarPricesArray = json_decode($response, true)['Offers'] ?? [];

        $filteredArray = [];
        foreach ($calendarPricesArray as $flight) {
            $outdep = explode(' ', $flight['outdep'])[0];
            if (!isset($filteredArray[$outdep]) || $flight['fltSellpricepp'] < $filteredArray[$outdep]['fltSellpricepp']) {
                $filteredArray[$outdep] = $flight;
            }
        }

        return response()->json([
            'flight' => $request->input('flight'),
            'calendarPricesArray' => array_values($filteredArray),
        ]);
    }

    public function bookingSummary(Request $request)
    {
        $passengers = $request->input('passengers');
        $flight = $request->input('flight');
        $totalPrice = $request->input('totalPrice');
        $apiUrl = env('API_URL');
        $depdate = explode(' ', $flight['outdep'])[0];

        $queryParams = [
            'agtid' => '144',
            'page' => 'HTLSEARCH',
            'platform' => 'WEB',
            'countryid' => '1',
            'regionid' => '0',
            'areaid' => '0',
            'resortid' => '0',
            'depdate' => $depdate,
            'flex' => '',
            'board' => '',
            'star' => '',
            'adults' => count($passengers),
            'children' => '0',
            'duration' => $flight['nights'],
            'hotelid' => '0',
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $hotelsArray = json_decode(trim($response), true)['Offers'] ?? [];
        $hotels = array_slice($hotelsArray, 0, 10);

        return response()->json([
            'passengers' => $passengers,
            'flight' => $flight,
            'totalPrice' => $totalPrice,
            'hotels' => $hotels,
        ]);
    }

    public function flightPayment(Request $request)
    {
        if (!Session::has('expiry_time')) {
            $sessionLifetime = (int) config('session.lifetime', 30);
            $expiryTime = now()->addMinutes($sessionLifetime);
            Session::put('expiry_time', $expiryTime);
        } else {
            $expiryTime = Session::get('expiry_time');
        }

        $formattedExpiryTime = $expiryTime->toIso8601String();
        $bookingId = strtoupper(Str::random(8));

        $passengers = $request->input('passengers');
        $flight = $request->input('flight');
        $total = $request->input('total');

        return response()->json([
            'passengers' => $passengers,
            'flight' => $flight,
            'totalprice' => $total,
            'booking_id' => $bookingId,
            'session_expiry' => $formattedExpiryTime,
        ]);
    }
}

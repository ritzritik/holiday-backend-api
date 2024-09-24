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
    public function index(Request $request): \Inertia\Response
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
        if (!is_array($flights)) {
            $flights = [];
        }

        // Apply sorting
        if (request('sort')) {
            usort($flights, function ($a, $b) {
                switch (request('sort')) {
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

        $budgetFilter = request('budget');
        if (isset($priceRanges[$budgetFilter])) {
            [$minPrice, $maxPrice] = $priceRanges[$budgetFilter];
            $flights = array_filter($flights, function ($flight) use ($minPrice, $maxPrice) {
                return ((float) $flight['fltSellpricepp']) >= $minPrice && ((float) $flight['fltSellpricepp']) <= $maxPrice;
            });
        }

        // Apply stops filtering (example: you may need to adjust this if stops data is available)
        $stopsFilter = request('stops');
        if ($stopsFilter && $stopsFilter !== 'any') {
            // Example filter logic (assuming stops data is available in your API response)
            // You need to add your own logic for stops filtering based on available data
        }

        return Inertia::render('Flights/Index', [
            'flights' => $flights,
            'queryParams' => request()->query() ?: null,
            'success' => session('success'),
        ]);
    }

    public function alternate_flights(Request $request)
    {
        if ($request->isMethod('post')) {
            $origin = $request->input('flightSearch')['origin'];
            $stay = $request->input('flightSearch')['stay'];
            $flex = $request->input('flightSearch')['flex'];
            $adults = $request->input('flightSearch')['adults'];
            $arrivalAirport = $request->input('flightSearch')['arrivalAirport'];
            $departureDate = $request->input('flightSearch')['departureDate'];
            // Convert the departure date to 'dd/mm/yyyy' format
            $formattedDepdate = \Carbon\Carbon::createFromFormat('Y-m-d', $departureDate)->format('d/m/Y');
            $apiUrl = env('API_URL');
            // Prepare the query parameters
            $queryParams = [
                'agtid' => '144',
                'page' => 'FLTSEARCH',
                'platform' => 'WEB',
                'depart' => $origin,
                'arrive' => $arrivalAirport,
                'depdate' => $formattedDepdate,
                'flex' => $flex,
                'duration' => $stay,
                'output' => 'JSON',
            ];
            $queryString = http_build_query($queryParams);
            $response = file_get_contents("{$apiUrl}?{$queryString}");
            $flights = json_decode($response, true);
            $flightsArray = $flights['Offers'] ?? [];
            // Return the flights data as JSON
            // return response()->json(['flights' => $flightsArray]);
            $flights = array_slice($flightsArray, 0, 2);
            return Response::json(['flights' => $flights]);
        }

        // return redirect()->back()->with('error', 'Invalid request method.');
        return Response::json(['error' => 'Invalid request method.'], 400);
    }

    public function flight_search(Request $request)
    {
        $apiUrl = env('API_URL');
        $depdate = $request->input('departureDate');
        $formattedDepdate = \Carbon\Carbon::createFromFormat('Y-m-d', $depdate)->format('d/m/Y');
        $adults = (int) $request->input('adults', 1);
        $children = (int) $request->input('children', 0);

        // Extract the number of days from the duration string
        $duration = $request->input('duration');
        $formattedDuration = preg_replace('/\D/', '', $duration);

        // Prepare the query parameters
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
        $rawData = file_get_contents("{$apiUrl}?{$queryString}");
        $trimmedData = trim($rawData);
        $response = mb_convert_encoding($trimmedData, 'UTF-8', 'UTF-8');
        $flights = json_decode($response, true)['Offers'] ?? [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
        }
        $flightArray = is_array($flights) ? $flights : [];

        // Calculate and add total price per flight
        foreach ($flightArray as &$flight) {
            $pricePerPerson = (float) ($flight['fltSellpricepp'] ?? 0);

            // Ensure adults and children have default values if not provided
            $adultsCount = isset($adults) ? (int) $adults : 1;
            $childrenCount = isset($children) ? (int) $children : 0;

            // Calculate total price based on available data
            $totalPrice = $pricePerPerson * ($adultsCount + $childrenCount);

            // Add total price and counts to each flight
            $flight['totalPrice'] = $totalPrice;
            $flight['adults'] = $adultsCount;
            $flight['children'] = $childrenCount;
        }


        // Apply sorting
        $sortOption = $request->input('sort', '');
        if ($sortOption) {
            usort($flightArray, function ($a, $b) use ($sortOption) {
                $priceA = $a['totalPrice'] ?? 0;
                $priceB = $b['totalPrice'] ?? 0;

                switch ($sortOption) {
                    case 'price_asc':
                        return $priceA <=> $priceB;
                    case 'price_desc':
                        return $priceB <=> $priceA;
                    default:
                        return 0;
                }
            });
        }

        // Apply budget filter
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
            $flightArray = array_filter($flightArray, function ($package) use ($minPrice, $maxPrice) {
                $totalPrice = $package['totalPrice'] ?? 0;
                return $totalPrice >= $minPrice && $totalPrice <= $maxPrice;
            });
        }

        // Apply departure date filtering
        if ($request->input('departure_date')) {
            $departureDate = $request->input('departure_date');
            if ($departureDate) {
                $depdate = \Carbon\Carbon::createFromFormat('Y-m-d', $departureDate)->format('d/m/Y');
            } else {
                $depdate = date('d/m/Y');
            }

            $queryParams = [
                'agtid' => '144',
                'page' => 'FLTSEARCH',
                'platform' => 'WEB',
                'depart' => $request->input('origin', 'LGW'),
                'arrive' => $request->input('destination', 'PFO'),
                'depdate' => $depdate,
                'flex' => '',
                'duration' => $formattedDuration,
                'output' => 'JSON',
            ];

            $queryString = http_build_query($queryParams);
            $rawData = file_get_contents("{$apiUrl}?{$queryString}");
            $trimmedData = trim($rawData);
            $response = mb_convert_encoding($trimmedData, 'UTF-8', 'UTF-8');
            $flights = json_decode($response, true)['Offers'] ?? [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
            }
            $flightArray = is_array($flights) ? $flights : [];
        }

        if ($request->expectsJson()) {
            return response()->json([
                'flights' => $flightArray,
                'queryParams' => $request->query() ?: null,
            ]);
        } else {
            return Inertia::render('Flights/FlightSearch', [
                'flights' => $flightArray,
                'queryParams' => request()->query() ?: null,
            ]);
        }
    }



    public function booking_details(Request $request): \Inertia\Response
    {
        $flight = $request->input('flight');

        $depdate = explode(' ', $flight['outdep'])[0];
        $apiUrl = env('API_URL');
        // http://87.102.127.86:8119/search/searchoffers.dll?agtid=100&page=FLTSEARCH&platform=WEB&depart=LGW&arrive=BFS&depdate=08/09/2024&flex=15&duration=15&output=JSON
        // Gather query parameters
        //http://87.102.127.86:8119/search/searchoffers.dll?agtid=100&page=FLTSEARCH&platform=WEB&depart=LGW&arrive=BFS&depdate=BFS&flex=15&duration=15&output=JSON
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
        if (!is_array($calendarPricesArray)) {
            $calendarPricesArray = [];
        }

        $filteredArray = [];

        foreach ($calendarPricesArray as $flight) {
            $outdep = explode(' ', $flight['outdep'])[0]; // Extracting the date part from 'outdep'

            // Check if we've already encountered this 'outdep' date
            if (!isset($filteredArray[$outdep])) {
                // If not, add it to the filtered array
                $filteredArray[$outdep] = $flight;
            } else {
                // If yes, compare the current price with the stored one
                if ($flight['fltSellpricepp'] < $filteredArray[$outdep]['fltSellpricepp']) {
                    // Replace it with the current one if the current price is lower
                    $filteredArray[$outdep] = $flight;
                }
            }
        }

        // Re-index the array to remove keys based on 'outdep' dates
        $filteredArray = array_values($filteredArray);
        return Inertia::render('Flights/FlightBookingPage', [
            'flight' => $request->input('flight'),
            'calendarPricesArray' => $filteredArray,
        ]);
    }

    public function booking_summary(Request $request): \Inertia\Response
    {
        $passengers = $request->input('passengers');
        $flight = $request->input('flight');
        $totalPrice = $request->input('totalPrice');
        // $deals = ["Deal 1: 20% off on first-class upgrade", "Deal 2: Free airport lounge access"];
        // $hotels = ["Hotel A: 4-star rating", "Hotel B: 5-star rating"];
        $apiUrl = env('API_URL');
        $depdate = explode(' ', $request->input('flight')['outdep'])[0];

        // Prepare the query parameters
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
            'duration' => $request->input('flight')['nights'],
            'hotelid' => '0',
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $response = trim($response); // Remove any hidden characters or BOM
        // Convert to UTF-8
        $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');
        $hotelsArray = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('JSON Decode Error: ' . json_last_error_msg(), $response);
        }
        $hotels = $hotelsArray['Offers'] ?? [];
        $hotels = array_slice($hotels, 0, 10);
        return Inertia::render('Flights/FlightBookingSummary', [
            'passengers' => $passengers,
            'flight' => $flight,
            'totalPrice' => $totalPrice,
            // 'deals' => $deals,
            'hotels' => $hotels
        ]);
    }

    public function flight_payment(Request $request): \Inertia\Response
    {
        if (!Session::has('expiry_time')) {
            $sessionLifetime = (int) config('session.lifetime', 30); // Ensure it's an integer
            $expiryTime = now()->addMinutes($sessionLifetime);
            Session::put('expiry_time', $expiryTime);
        } else {
            $expiryTime = Session::get('expiry_time');
        }

        $formattedExpiryTime = $expiryTime->toIso8601String();

        // Auto-generating a unique booking ID
        $bookingId = strtoupper(Str::random(8));

        //passengers : passengers, flight : flight, hotel : hotel, total: total
        $passengers = $request->input('passengers');
        $flight = $request->input('flight');
        $total = $request->input('total');
        return Inertia::render('Flights/FlightPayment', [
            'passengers' => $passengers,
            'flight' => $flight,
            'totalprice' => $total,
            'booking_id' => $bookingId,
            'session_expiry' => $formattedExpiryTime,
        ]);
    }
}

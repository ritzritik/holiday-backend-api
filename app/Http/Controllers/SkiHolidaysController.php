<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SkiHolidaysController extends Controller
{
    // public function index(): Response
    // {
    //     return Inertia::render('SkiHolidays/Index', []);
    // }

    public function ski_holiday_search(Request $request)
    {
        $apiUrl = env('API_URL');


        //http://87.102.127.86:8119/search/searchoffers.dll?agtid=100&page=SKISEARCH&platform=WEB&depart=LGW|STN|LHR|LCY|SEN|LTN&countryid=1&resortid=0&depdate=07/01/2020&flex=3&board=&star=&adults=2&children=0&duration=7&hotelid=&minprice=0.00&maxprice=10000.00&output=XML

        // Convert the departure date to 'dd/mm/yyyy' format
        $depdate = $request->input('departureDate');
        $formattedDepdate = \Carbon\Carbon::createFromFormat('Y-m-d', $depdate)->format('d/m/Y');
        $duration = $request->input('duration');
        $formattedDuration = preg_replace('/\D/', '', $duration);

        // Prepare the query parameters
        $queryParams = [
            'agtid' => '144',
            'page' => 'SKISEARCH',
            'platform' => 'WEB',
            'depart' => 'LGW|STN|LHR|LCY|SEN|LTN',
            'countryid' => $request->input('destination'),
            'resortid' => 0,
            'depdate' => $formattedDepdate,
            'flex' => '',
            'board' => '',
            'star'=> '',
            'adults' => $request->input('adults', 2),
            'children' => 0,
            'duration' => $formattedDuration,
            'hotelid' => '',
            'minprice' => 0.00,
            'maxprice' => 10000.00,
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        dd($queryString);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $ski = json_decode($response, true);

        $skiArray = $ski['Offers'] ?? [];

        return response()->json([
            'skis' => $skiArray
        ]);
    }

    public function ski_booking_details(Request $request)
    {
        $package = $request['package'];
        $countries = Country::fetchFromApi();
        $apiUrl = env('API_URL');
        // http://87.102.127.86:8119/search/searchoffers.dll?agtid=100&page=CALSEARCH&platform=WEB&depart=LGW&Monthyear=08/2024&board=BB&adults=2&children=0&duration=7&type=DPCP&output=JSON&hotelid=19711
        // Gather query parameters
        $queryParams = [
            'agtid' => '144',
            'page' => 'CALSEARCH',
            'platform' => 'WEB',
            'depart' => $package['Depaptcode'],
            'Monthyear' => date('m/Y'),
            'board' => $package['Boardbasis'],
            'adults' => 2,
            'children' => 0,
            'duration' => $package['Duration'],
            'type' => $package['Type'],
            'output' => 'JSON',
            'hotelid' => $package['Ourhtlid']
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");
        $calendarPricesArray = json_decode($response, true)['Offers'] ?? [];
        if (!is_array($calendarPricesArray)) {
            $calendarPricesArray = [];
        }


        //http://87.102.127.86:8119/search/searchoffers.dll?agtid=100&page=BROCHURE&brochurecode=BEWE-AMTSES1CO0&output=JSON
        $queryParams = [
            'agtid' => '144',
            'page' => 'BROCHURE',
            'brochurecode' => $package['Brochurecode'],
            'output' => 'JSON',
        ];

        $queryString = http_build_query($queryParams);
        $response = file_get_contents("{$apiUrl}?{$queryString}");

        $cleanedResponse = preg_replace('/,(\s*})/', '$1', $response);

        // Decode the cleaned JSON
        $hotelInfoArray = json_decode($cleanedResponse, true);

        // Check for errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo 'JSON Error: ' . json_last_error_msg();
        } else {
            $hotelInfoArray = $hotelInfoArray['content'] ?? [];
        }
        return response()->json([
            'countries' => $countries,
            'selected_package' => $package,
            'calendarPricesArray' => $calendarPricesArray,
            'hotelInfo' => $hotelInfoArray
            // 'theme' => $theme,
            // 'hotels' => $hotels,
        ]);
    }

    public function ski_checkout(Request $request)
    {
        // Simulating a package checkout session expiry time (e.g., 30 minutes from now)
        // $expiryTime = now()->addMinutes(30)->format('H:i:s');
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

        $checkout_package = $request->input('selected_package');

        return response()->json([
            'checkout_package' => $checkout_package,
            'booking_id' => $bookingId,
            'session_expiry' => $formattedExpiryTime,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PassengerDetails;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;
use App\Models\Pricing;
use Illuminate\Support\Facades\Http;
use Psy\Output\Theme;
use App\Models\BookingDetails;
use App\Models\CardDetails;

class PackagesController extends Controller
{
    public function index(Request $request)
    {
        $queryParams = $this->buildQueryParams($request, [
            'agtid' => '144',
            'page' => 'HOLSEARCH',
            'platform' => 'WEB',
            'depart' => 'LGW|STN|LHR|LCY|SEN|LTN',
            'countryid' => '1',
            'regionid' => '4',
            'areaid' => '9',
            'resortid' => '0',
            'depdate' => now()->format('d/m/Y'),
            'flex' => '3',
            'adults' => '2',
            'children' => '0',
            'duration' => '7',
            'minprice' => '0.00',
            'maxprice' => '0.00',
            'type' => 'DPCP',
            'output' => 'JSON',
        ]);

        $packages = $this->getPackagesFromApi($queryParams);
        $filteredPackages = $this->filterPackages($packages, $request);

        return response()->json([
            'message' => 'success',
            'data' => [
            'packages' => $filteredPackages,
            'bestSellingPackages' => $this->getBestSellingPackages($filteredPackages),
            'themes' => $this->getThemes(),
            'hotels' => $this->getHotels($filteredPackages),
            'testimonials' => $this->getTestimonials(), ],
            'statusCode' => 200
        ]);
    }

    // Search Packages with dynamic query params
    public function packageSearch(Request $request)
    {
        $apiUrl = 'http://87.102.127.86:8119/search/searchoffers.dll';
        $depdate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('departureDate'))->format('d/m/Y');
        $duration = preg_replace('/\D/', '', $request->input('duration')); 
        $adults = preg_replace('/\D/', '', $request->input('adults'));
        $airportCode = $request->input('aiportCode');
        $flex = $request->input('flex');
        $queryParams = $this->buildQueryParams($request, [
            'agtid' => '100',
            'page' => 'HOLSEARCH',
            'platform' => 'WEB',
            'depart' => $airportCode,
            'countryid' => $request->input('destination'),
            'depdate' => $depdate,
            'flex' => $flex,
            'board' => '',
            'star' => '',
            'adults' => $adults,
            'children' => '0',
            'duration' => $duration,
            'minprice' => $request->input('minprice', ''),
            'maxprice' => $request->input('maxprice', ''),
            'type' => $request->input('type', 'DPCP'),
            'output' => 'JSON',
        ]);
    
        $queryString = http_build_query($queryParams);
        $rawData = file_get_contents("{$apiUrl}?{$queryString}");
        $trimmedData = trim($rawData);
        $response = mb_convert_encoding($trimmedData, 'UTF-8', 'UTF-8');
        $packages = json_decode($response, true)['Offers'] ?? [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
        }
        $packagesArray = is_array($packages) ? $packages : [];
        $packagesArray = array_slice($packages, 0, 60); 
    
        $sortOption = $request->input('sort', ''); // Default to an empty string if not set
        // Apply sorting if requested
        if ($sortOption) {
            usort($packagesArray, function ($a, $b) use ($sortOption) {
                $sellPriceA = $a['Sellprice'] ?? 0;
                $sellPriceB = $b['Sellprice'] ?? 0;
                $durationA = $a['Duration'] ?? 0;
                $durationB = $b['Duration'] ?? 0;
    
                switch ($sortOption) {
                    case 'price_asc':
                        return $sellPriceA <=> $sellPriceB;
                    case 'price_desc':
                        return $sellPriceB <=> $sellPriceA;
                    case 'duration_asc':
                        return $durationA <=> $durationB;
                    case 'duration_desc':
                        return $durationB <=> $durationA;
                    default:
                        return 0;
                }
            });
        }
    
        // Apply budget filter if requested
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
            $packagesArray = array_filter($packagesArray, function ($package) use ($minPrice, $maxPrice) {
                return isset($package['Sellprice']) && ((float) $package['Sellprice']) >= $minPrice && ((float) $package['Sellprice']) <= $maxPrice;
            });
        }
    
        // Apply activity filtering if requested
        if ($request->input('activities')) {
            $selectedActivities = $request->input('activities', []);
            $packagesArray = array_filter($packagesArray, function ($package) use ($selectedActivities) {
                foreach ($selectedActivities as $activity) {
                    if (strpos($package['Activities'], $activity) === false) {
                        return false;
                    }
                }
                return true;
            });
        }
    
        // Apply departure date filtering if requested
        if ($request->input('departure_date')) {
            $departureDate = $request->input('departure_date');
            $packagesArray = array_filter($packagesArray, function ($package) use ($departureDate) {
                return $package['OutboundDep'] === $departureDate;
            });
        }
        
        return response()->json([
            'message' => 'success',
            'data' => [
                'packages' => $packagesArray,
                'queryParams' => $request->query() ?: null,
            ],
            'statusCode' => 200
        ]);
    }

    // Helper: Build Query Parameters
    private function buildQueryParams(Request $request, array $defaultParams): array
    {
        return array_merge($defaultParams, $request->only([
            'agtid', 'page', 'platform', 'depart', 'countryid', 'regionid', 'areaid', 'resortid',
            'depdate', 'flex', 'board', 'star', 'adults', 'children', 'duration', 'minprice', 'maxprice', 'type'
        ]));
    }

    // Helper: Fetch Packages from External API
    private function getPackagesFromApi(array $queryParams): array
    {
        $queryString = http_build_query($queryParams);
        $cacheKey = md5($queryString);
        return Cache::remember($cacheKey, 3600, function () use ($queryString) {
            $apiUrl = 'http://87.102.127.86:8119/search/searchoffers.dll';
            $response = Http::get("{$apiUrl}?{$queryString}")->body();
            $response = mb_convert_encoding(trim($response), 'UTF-8', 'UTF-8');
            $packages = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
            }
            return $packages['Offers'] ?? [];
        });
    }

    // Helper: Filter Packages
    private function filterPackages(array $packages, Request $request): array
    {
        return array_filter($packages, function ($package) use ($request) {
            $sellPrice = $package['Sellprice'] ?? 0;
            return ($request->input('minprice') == 0.00 || $sellPrice >= $request->input('minprice')) &&
                   ($request->input('maxprice') == 0.00 || $sellPrice <= $request->input('maxprice')) &&
                   ($request->input('star') == '' || $package['Starrating'] == $request->input('star')) &&
                   ($request->input('board') == '' || $package['Boardbasis'] == $request->input('board')) &&
                   ($request->input('duration') == '' || $package['Duration'] == $request->input('duration'));
        });
    }

    // Helper: Sort and Apply Filters
    private function sortAndFilterPackages(array $packages, Request $request): array
    {
        $sortOption = $request->input('sort', '');
        if ($sortOption) {
            usort($packages, function ($a, $b) use ($sortOption) {
                $sellPriceA = $a['Sellprice'] ?? 0;
                $sellPriceB = $b['Sellprice'] ?? 0;
                switch ($sortOption) {
                    case 'price_asc':
                        return $sellPriceA <=> $sellPriceB;
                    case 'price_desc':
                        return $sellPriceB <=> $sellPriceA;
                    default:
                        return 0;
                }
            });
        }

        // Apply budget filter if requested
        if ($budgetFilter = $request->input('budget')) {
            $packages = $this->applyBudgetFilter($packages, $budgetFilter);
        }

        return $packages;
    }

    // Helper: Apply Budget Filter
    private function applyBudgetFilter(array $packages, string $budgetFilter): array
    {
        $priceRanges = [
            'under_100' => [0, 100],
            '100_500' => [100, 500],
            '500_1000' => [500, 1000],
            '1000_2000' => [1000, 2000],
            'over_2000' => [2000, PHP_INT_MAX],
        ];

        [$minPrice, $maxPrice] = $priceRanges[$budgetFilter] ?? [0, PHP_INT_MAX];
        return array_filter($packages, function ($package) use ($minPrice, $maxPrice) {
            return isset($package['Sellprice']) && $package['Sellprice'] >= $minPrice && $package['Sellprice'] <= $maxPrice;
        });
    }

    // Helper: Get Best-Selling Packages
    private function getBestSellingPackages(array $filteredPackages): array
    {
        // Remove any packages that don't have the "Sellprice" key
        $filteredPackages = array_filter($filteredPackages, function ($package) {
            return isset($package['Sellprice']);
        });
    
        // Sort the remaining packages by "Sellprice"
        usort($filteredPackages, function ($a, $b) {
            return (integer)$a['Sellprice'] <=> (integer)$b['Sellprice'];
        });
    
        // Return the top 10 packages
        return array_slice($filteredPackages, 0, 10);
    }
    

    // Helper: Get Hotels
    private function getHotels(array $packages): array
    {
        $slicedHotels = array_slice($packages, 0, 4);
        $defaultDescription = "Experience unmatched luxury and elegance at our 5-star hotel, located just minutes away from the city's top attractions.";

        return array_map(function ($hotel) use ($defaultDescription) {
            return [
                'id' => $hotel['Ourhtlid'],
                'supplier_id' => $hotel['Supphtlid'],
                'name' => $hotel['Hotelname'],
                'description' => $hotel['Content'] ?? $defaultDescription,
                'room' => $hotel['Roomtype'],
                'brochure' => $hotel['Brochurecode'],
                'image' => $hotel['Image'] ?? asset('assets/images/hotel.jpg'),
                'price' => $hotel['Hotelnetprice'],
            ];
        }, $slicedHotels);
    }


    private function getTestimonials(): array
    {
        return [
            ['user' => 'John Doe', 'message' => 'Great experience!', 'image' => asset('assets/images/testimonial.png')],
            ['user' => 'Jane Smith', 'message' => 'Loved the trip!', 'image' => asset('assets/images/testimonial.png')],
        ];
    }

    public function packages_by_theme(Request $request)
    {
        $theme = $request->input('theme');
        // dd($theme);
        // The API URL from the environment file
        $apiUrl = 'http://87.102.127.86:8119/search/searchoffers.dll';
    
        // Gather query parameters
        $queryParams = [
            'agtid' => '144',
            'page' => 'HOLSEARCH',
            'platform' => 'WEB',
            'depart' => $request->input('depart', 'LGW|STN|LHR|LCY|SEN|LTN'),
            'countryid' => $request->input('countryid', '1'),
            'regionid' => $request->input('regionid', '4'),
            'areaid' => $request->input('areaid', '9'),
            'resortid' => $request->input('resortid', '0'),
            'depdate' => date('d/m/Y'),
            'flex' => $request->input('flex', '3'),
            'board' => $request->input('board', ''),
            'star' => $request->input('star', ''),
            'adults' => $request->input('adults', '2'),
            'children' => $request->input('children', '0'),
            'duration' => $request->input('duration', '7'),
            'minprice' => $request->input('minprice', '0.00'),
            'maxprice' => $request->input('maxprice', '0.00'),
            'type' => $request->input('type', 'DPCP'),
            'output' => 'JSON',
        ];
    
        $queryString = http_build_query($queryParams);
        $cacheKey = 'packages_' . md5($queryString);
    
        // $packagesArray = Cache::remember($cacheKey, 60 * 60 * 12, function () use ($apiUrl, $queryString) {
        $response = file_get_contents("{$apiUrl}?{$queryString}");

        $trimmedData = trim($response);
        $response = mb_convert_encoding($trimmedData, 'UTF-8', 'UTF-8');
        $packagesArray = json_decode($response, true)['Offers'] ?? [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON Decode Error: ' . json_last_error_msg());
        }
        // $packagesArray = json_decode($response, true)['Offers'] ?? [];
        //     return is_array($packages) ? $packages : [];
        // });

        $sortOption = $request->input('sort', ''); // Default to an empty string if not set
        // Apply sorting if requested
        if ($sortOption) {
            usort($packagesArray, function ($a, $b) use ($sortOption) {
                $sellPriceA = $a['Sellprice'] ?? 0;
                $sellPriceB = $b['Sellprice'] ?? 0;
                // $durationA = $a['Duration'] ?? 0;
                // $durationB = $b['Duration'] ?? 0;
    
                switch ($sortOption) {
                    case 'price_asc':
                        return $sellPriceA <=> $sellPriceB;
                    case 'price_desc':
                        return $sellPriceB <=> $sellPriceA;
                    // case 'duration_asc':
                    //     return $durationA <=> $durationB;
                    // case 'duration_desc':
                    //     return $durationB <=> $durationA;
                    default:
                        return 0;
                }
            });
        }
    
        // Apply budget filter if requested
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
            $packagesArray = array_filter($packagesArray, function ($package) use ($minPrice, $maxPrice) {
                return isset($package['Sellprice']) && ((float) $package['Sellprice']) >= $minPrice && ((float) $package['Sellprice']) <= $maxPrice;
            });
        }
    
        // Apply activity filtering if requested
        // if ($request->input('activities')) {
        //     $selectedActivities = $request->input('activities', []);
        //     $packagesArray = array_filter($packagesArray, function ($package) use ($selectedActivities) {
        //         foreach ($selectedActivities as $activity) {
        //             if (strpos($package['Activities'], $activity) === false) {
        //                 return false;
        //             }
        //         }
        //         return true;
        //     });
        // }

        // Apply departure date filtering if requested
        if ($request->input('departure_date')) {
            $departureDate = $request->input('departure_date');

            // Check if departure date is provided and in the correct format
            if ($departureDate) {
                $depdate = \Carbon\Carbon::createFromFormat('Y-m-d', $departureDate)
                                        // ->subDay()  // Subtract one day
                                        ->format('d/m/Y');
            } else {
                // Default date
                $depdate = date('d/m/Y');
            }

            $queryParams = [
                'agtid' => '144',
                'page' => 'HOLSEARCH',
                'platform' => 'WEB',
                'depart' => $request->input('depart', 'LGW|STN|LHR|LCY|SEN|LTN'),
                'countryid' => $request->input('countryid', '1'),
                'regionid' => $request->input('regionid', '4'),
                'areaid' => $request->input('areaid', '9'),
                'resortid' => $request->input('resortid', '0'),
                'depdate' => $depdate,
                'flex' => $request->input('flex', '3'),
                'board' => $request->input('board', ''),
                'star' => $request->input('star', ''),
                'adults' => $request->input('adults', '2'),
                'children' => $request->input('children', '0'),
                'duration' => $request->input('duration', '7'),
                'minprice' => $request->input('minprice', '0.00'),
                'maxprice' => $request->input('maxprice', '0.00'),
                'type' => $request->input('type', 'DPCP'),
                'output' => 'JSON',
            ];
        
            $queryString = http_build_query($queryParams);
            // $cacheKey = 'packages_' . md5($queryString);
        
            // $packagesArray = Cache::remember($cacheKey, 60 * 60 * 12, function () use ($apiUrl, $queryString) {
            $response = file_get_contents("{$apiUrl}?{$queryString}");
            $packagesArray = json_decode($response, true)['Offers'] ?? [];
            // return is_array($packages) ? $packages : [];
            // });
        }

        return response()->json([
            'message' => 'success',
            'data' => [
                'packages' => $packagesArray,
                'queryParams' => $request->query() ?: null,
                'theme' => $theme,
            ],
            'statusCode' => 200
        ]); 
    }
    

    public function package_booking_details(Request $request)
    {
        $package = $request['package'];
    
        $countries = Cache::remember('countries', 60 * 60 * 24, function () {
            return Country::all()->pluck('name', 'country_api_id');
        });
    
        $apiUrl = 'http://87.102.127.86:8119/search/searchoffers.dll';
    
        // Cache calendar prices
        $calendarPricesCacheKey = 'calendar_prices_' . md5(json_encode($package));
        $calendarPricesArray = Cache::remember($calendarPricesCacheKey, 60 * 60 * 12, function () use ($package, $apiUrl) {
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
            $calendarPrices = json_decode($response, true)['Offers'] ?? [];
            return is_array($calendarPrices) ? $calendarPrices : [];
        });
    
        // Cache hotel info
        $hotelInfoCacheKey = 'hotel_info_' . md5($package['Brochurecode']);
        $hotelInfoArray = Cache::remember($hotelInfoCacheKey, 60 * 60 * 12, function () use ($package, $apiUrl) {
            $queryParams = [
                'agtid' => '144',
                'page' => 'BROCHURE',
                'brochurecode' => $package['Brochurecode'],
                'output' => 'JSON',
            ];
    
            $queryString = http_build_query($queryParams);
            $response = file_get_contents("{$apiUrl}?{$queryString}");
    
            $cleanedResponse = preg_replace('/,(\s*})/', '$1', $response);
            $hotelInfo = json_decode($cleanedResponse, true);
    
            return $hotelInfo['content'] ?? [];
        });
    
        // Return data as JSON instead of rendering a view
        return response()->json([
            'message' => 'success',
            'data' => [
            'countries' => $countries,
            'selected_package' => $package,
            'calendarPricesArray' => $calendarPricesArray,
            'hotelInfo' => $hotelInfoArray ],
            'statusCode' => 200
        ]);
    }
    

    public function package_checkout(Request $request)
    {
        if (!Session::has('expiry_time')) {
            $sessionLifetime = (int) config('session.lifetime', 30);
            $expiryTime = now()->addMinutes($sessionLifetime);
            Session::put('expiry_time', $expiryTime);
        } else {
            $expiryTime = Session::get('expiry_time');
        }
    
        $formattedExpiryTime = $expiryTime->toIso8601String();
        // Auto-generating a unique booking ID
        $bookingId = strtoupper(Str::random(8));
        // Return data as JSON
        return response()->json([
            'message' => 'success',
            'data' => [
                'booking_id' => $bookingId,
                'session_expiry' => $formattedExpiryTime,
            ],
            'statusCode' => 200
        ]);
    }
    

    
    private function getThemes(): array
    {
        return [
            ['name' => 'Honeymoon', 'image' => asset('assets/images/honeymoon.jpg')],
            ['name' => 'Family', 'image' => asset('assets/images/family.jpg')],
            ['name' => 'Friends', 'image' => asset('assets/images/friends.jpg')],
            ['name' => 'Solo', 'image' => asset('assets/images/solo.jpg')],
            ['name' => 'Nature', 'image' => asset('assets/images/nature.jpg')],
            ['name' => 'Adventure', 'image' => asset('assets/images/adventure.jpg')],
            ['name' => 'Luxury', 'image' => asset('assets/images/luxury.jpg')],
            ['name' => 'Cultural', 'image' => asset('assets/images/cultural.jpg')],
        ];
    }

    // //Checking for Login User
    // public function checkLogin()
    // {
    //     return response()->json(['isLoggedIn' => Auth::check()]);
    // }

    // public function login(Request $request)
    // {
    //     // Validate credentials
    //     $credentials = $request->only('email', 'password');

    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     if (Auth::attempt($credentials)) {
    //         // Authentication passed, return success response
    //         return response()->json(['status' => 'success', 'user' => Auth::user()]);
    //     }

    //     return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    // }

    public function storePassengerDetails(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'passengers' => 'required|array',
                'passengers.*.booking_id' => 'nullable|string|max:100',
                'passengers.*.title' => 'nullable|in:Mr,Ms,Mrs,Miss,Dr',
                'passengers.*.first_name' => 'required|string|max:255',
                'passengers.*.surname' => 'required|string|max:255',
                'passengers.*.email' => 'required|string|email|max:255',
                'passengers.*.contact_number' => 'required|string|max:15',
                'passengers.*.package_type' => 'nullable|string',
                'passengers.*.price' => 'required|numeric',
                'booking_details' => 'required|array',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $passengerDetails = [];
    
            // Prepare booking details
            $bookingDetailsData = $request->input('booking_details');
            // $bookingDetailsData['booking_id'] = $bookingId; // Add the booking ID to the booking details
    
            // Save booking details, storing package_details as JSON
            $bookingDetails = BookingDetails::create([
                'booking_id' => $bookingDetailsData['booking_id'],
                'refnum' => isset($bookingDetailsData['refnum']) ? $bookingDetailsData['refnum'] : null,
                'package_details' => json_encode($bookingDetailsData), // Store as JSON
            ]);
    
            // Loop over passengers and save them
            foreach ($request->input('passengers') as $passengerData) {
                $passengerData['booking_id'] = $bookingDetails->booking_id; // Assign booking ID to passengers
                $passengerDetails[] = PassengerDetails::create($passengerData);
            }
    
            return response()->json([
                'message' => 'Passenger and booking details saved successfully.',
                'bookingDetails' => $bookingDetails,
                'passengerDetails' => $passengerDetails,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    


    public function verifyPromoCode(Request $request)
    {
        $promoCode = $request->input('promo_code');
        $coupon = Coupon::where('coupon_code', $promoCode)
            ->where('active', 1)
            ->where('is_deleted', 0)
            ->where('is_expired', 0)
            ->first();

        if ($coupon) {
            return response()->json(['success' => true, 'discount' => $coupon->discount]);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid promo code']);
        }
    }

    public function saveBookingDetails(Request $request)
    {
        try {
            // Validate the incoming booking request
            $validator = Validator::make($request->all(), [
                'booking_id' => 'nullable|string|max:100',
                'refnum' => 'required|string|max:100',
                'type' => 'required|string|max:10',
                'hotelsupplier' => 'required|string|max:255',
                'flightsuppler' => 'required|string|max:255',
                'depaptcode' => 'required|string|max:10',
                'arraptcode' => 'required|string|max:10',
                'depaptname' => 'required|string|max:255',
                'arraptname' => 'required|string|max:255',
                'duration' => 'required|numeric',
                'flightnetprice' => 'required|numeric',
                'hotelnetprice' => 'required|numeric',
                'baggageprice' => 'nullable|numeric',
                'boardbasis' => 'nullable|string|max:255',
                'hotelname' => 'required|string|max:255',
                'image' => 'nullable|string|max:255',
                'starrating' => 'nullable|numeric',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'outbounddep' => 'required|date_format:Y-m-d H:i',
                'outboundarr' => 'required|date_format:Y-m-d H:i',
                'outboundfltnum' => 'required|string|max:20',
                'inbounddep' => 'required|date_format:Y-m-d H:i',
                'inboundarr' => 'required|date_format:Y-m-d H:i',
                'inboundfltnum' => 'required|string|max:20',
                'sellprice' => 'required|numeric',
                'resortname' => 'required|string|max:255',
                'roomtype' => 'required|string|max:255',
                'giatacode' => 'nullable|string|max:50',
                'ourhtlid' => 'nullable|string|max:50',
                'supphtlid' => 'nullable|string|max:50',
                'brochurecode' => 'nullable|string|max:50',
                'tticode' => 'nullable|string|max:50',
                'content' => 'nullable|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Create booking details
            $bookingDetails = BookingDetails::create($request->all());
    
            return response()->json([
                'message' => 'Booking details saved successfully.',
                'bookingDetails' => $bookingDetails,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    

    public function saveCardDetails(Request $request)
    {
        try {
            // Validate the card details
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|string|max:255',
                'card_number' => 'required|string|max:255',
                'card_holder_name' => 'required|string|max:255',
                'expiry_date' => 'required|string|max:50',
                'cvv' => 'required|string|max:4',
                'billing_address' => 'nullable|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $cardDetails = CardDetails::create($request->all());
    
            return response()->json([
                'success' => true,
                'message' => 'Card details saved successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    // public function getPassengerDetails()
    // {
    //     // Assuming you have a user authentication implemented and `user_id` is available
    //     $userId = auth()->user()->id;
    //     $passengerDetails = PassengerDetails::where('user_id', $userId)->get();

    //     return response()->json($passengerDetails);
    // }

    // public function getPricing()
    // {
    //     // Assuming you have a `Pricing` model to fetch the pricing details
    //     $pricing = Pricing::all();

    //     return response()->json($pricing);
    // }


            // $themeCountryMapping = [
        //     'Honeymoon' => [
        //         1
        //         // Ideal for romantic getaways
        //         // 1, 2, 9, 14, 15, 19, 20, 22, 23, 26, 27, 30, 31, 32, 33, 36, 37, 41, 43, 47, 48, 55, 61, 62, 64, 66, 68, 69, 70, 71, 73, 75, 77, 78, 79, 82, 83, 84, 85, 88, 89, 90, 93, 94, 96, 97
        //     ],
        //     'Family' => [
        //         3
        //         // Best for family vacations
        //         // 3, 5, 6, 7, 8, 10, 11, 12, 13, 21, 25, 29, 30, 31, 32, 33, 39, 41, 44, 50, 51, 52, 55, 57, 59, 61, 62, 63, 66, 67, 68, 73, 77, 78, 79, 81, 82, 84, 85, 86, 88, 90, 93, 94
        //     ],
        //     'Friends' => [
        //         7
        //         // Suitable for group travel
        //         // 7, 8, 9, 13, 21, 22, 23, 24, 30, 31, 32, 33, 39, 41, 44, 46, 50, 51, 52, 57, 59, 61, 62, 63, 64, 66, 67, 73, 75, 77, 78, 79, 82, 84, 85, 88, 89, 90
        //     ],
        //     'Solo' => [
        //         11
        //         // Ideal for solo travelers
        //         // 10, 11, 12, 14, 19, 20, 21, 22, 23, 26, 27, 29, 32, 33, 36, 37, 38, 41, 42, 47, 48, 50, 52, 55, 57, 59, 61, 62, 63, 64, 66, 68, 70, 71, 73, 77, 78, 79, 81, 82, 84, 86, 88, 90
        //     ],
        //     'Nature' => [
        //         22
        //         // Best for nature lovers
        //         // 13, 14, 15, 20, 22, 23, 24, 25, 30, 31, 32, 33, 44, 50, 51, 55, 57, 59, 61, 62, 63, 64, 66, 68, 70, 71, 73, 75, 77, 78, 79, 81, 82, 84, 85, 86, 88, 90
        //     ],
        //     'Adventure' => [
        //         30
        //         // Great for adventurous activities
        //         // 16, 17, 18, 20, 22, 23, 24, 25, 30, 31, 32, 33, 36, 37, 38, 44, 46, 50, 51, 52, 55, 57, 59, 61, 62, 64, 66, 68, 70, 71, 73, 75, 77, 78, 79, 82, 84, 85, 86, 88, 90
        //     ],
        //     'Luxury' => [
        //         19
        //         // Ideal for high-end experiences
        //         // 19, 20, 21, 26, 27, 29, 32, 33, 39, 41, 43, 47, 48, 50, 55, 57, 59, 61, 62, 63, 66, 67, 68, 70, 73, 77, 78, 79, 82, 84, 85, 86, 88, 90
        //     ],
        //     'Cultural' => [
        //         25
        //         // Perfect for cultural exploration
        //         // 22, 23, 24, 25, 30, 32, 33, 36, 37, 41, 43, 47, 48, 50, 52, 55, 57, 59, 61, 62, 63, 64, 66, 67, 68, 70, 71, 73, 75, 77, 78, 79, 81, 82, 84, 85, 86, 88, 90
        //     ],
        // ];

}

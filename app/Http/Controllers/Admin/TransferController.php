<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\AirportPricing;
use App\Models\Country;
use App\Models\NewsLetter;
use App\Models\Region;
use App\Models\TransferPricing;
// use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailable;

class TransferController extends Controller
{
    public function index()
    {
        $countries = Cache::remember('countries', 60 * 60 * 12, function () {
            $countriesPlucked = Country::all()->pluck('name', 'country_api_id');
            return $countriesPlucked->map(function ($name, $id) {
                return [
                    'id' => $id,
                    'name' => $name,
                ];
            })->values();
        });

        $regions = Cache::remember('regions', 60 * 60 * 12, function () {
            return Region::all(['id', 'region_api_id', 'name', 'country_id']);
        });

        return view('admin.transfer.index', compact('countries', 'regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer',
            'region_id' => 'required|integer',
            'standard_price' => 'required|numeric',
            'private_price' => 'required|numeric',
        ]);

        TransferPricing::updateOrCreate(
            [
                'country_id' => $request->country_id,
                'region_id' => $request->region_id,
            ],
            [
                'standard_price' => $request->standard_price,
                'private_price' => $request->private_price,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function fetchPricing()
    {
        $pricing = TransferPricing::with('country', 'region')->paginate(10);
        return response()->json($pricing);
    }

    public function fetchRegions($countryId)
    {
        $regions = Region::where('country_id', $countryId)->get(['id', 'name']);
        return response()->json($regions);
    }

    public function parking()
    {
        $pricingData = AirportPricing::with('airport')->paginate(20);

        // Fetch all airports for dropdown
        $airports = Airport::all();

        return view('admin.transfer.parking', [
            'airports' => $airports,
            'pricingData' => $pricingData
        ]);
    }

    public function getPricing($airportId)
    {
        // Fetch the pricing record for the selected airport
        $pricing = AirportPricing::where('airport_id', $airportId)->first();

        // Check if pricing exists
        if ($pricing) {
            return response()->json([
                'exists' => true,
                'private_parking_price' => $pricing->private_parking_price,
                'standard_parking_price' => $pricing->standard_parking_price,
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    // Function to save the prices
    public function setPricing(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'airport_id' => 'required|exists:airports,id',
            'private_parking_price' => 'required|numeric',
            'standard_parking_price' => 'required|numeric',
        ]);

        // Save or update the pricing record
        AirportPricing::updateOrCreate(
            ['airport_id' => $request->airport_id],
            [
                'private_parking_price' => $request->private_parking_price,
                'standard_parking_price' => $request->standard_parking_price,
            ]
        );

        // Redirect back with success message
        return redirect()->back()->with('success', 'Parking prices updated successfully.');
    }


    public function insurance()
    {
        return view('admin.transfer.insurance');
    }

    public function luggage()
    {
        return view('admin.transfer.luggage');
    }

    public function history()
    {
        return view('admin.transfer.history');
    }

    public function subscribers($id = null)
    {
        if ($id) {
            // Find the subscriber by ID and mark as read or remove
            $subscriber = NewsLetter::find($id);
            if ($subscriber) {
                $subscriber->delete();
            }
        }

        // Retrieve all subscribers or any relevant data
        $subscribers = NewsLetter::all(); // Adjust as needed

        return view('admin.transfer.subscribers', compact('subscribers'));
    }

    public function markAsRead($id)
    {
        $subscriber = NewsLetter::find($id);
        if ($subscriber) {
            // Mark as read or delete
            $subscriber->delete();
        }

        return response()->json(['status' => 'success']);
    }

    public function sendEmail(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Send email logic here, using Laravel's Mail facade or any other email service
        // Mail::to($validatedData['email'])->send(new CustomEmail($validatedData['subject'], $validatedData['message']));

        return response()->json(['success' => 'Email sent successfully!']);
    }
}

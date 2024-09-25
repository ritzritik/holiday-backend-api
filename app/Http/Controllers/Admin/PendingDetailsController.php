<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PassengerDetails;
use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\Package;
use App\Models\Holiday;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Payments;
use App\Models\CardDetails;

class PendingDetailsController extends Controller
{
    public function index()
    {
        return view('admin.pending.index');
    }

    // public function packages()
    // {
    //     $packages = Package::where(['booking',1])->get();  // Fetch package data
    //     return view('admin.booking.details', compact('packages'))->with('type', 'packages');
    // }

    public function flights()
    {
        $flights = Flight::where([['status', 0], ['is_deleted', 0]])->get();
        $userIds = $flights->pluck('created_by')->unique();

        // Fetch the corresponding user names
        $users = User::whereIn('id', $userIds)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('id');
        // Return only the partial view for AJAX
        return view('admin.pending.partial.flight', compact('flights', 'users'));
    }

    public function packages()
    {
        // Fetch all PassengerDetails with payment_status 0
        $passengerDetails = PassengerDetails::where('payment_status', 0)->get();

        // Group passenger details by booking_id
        $groupedByBooking = $passengerDetails->groupBy('booking_id');

        // Initialize an empty collection for packages
        $packages = collect();

        foreach ($groupedByBooking as $booking_id => $group) {
            // Get the first passenger detail as representative of the package
            $package = $group->first();
            // Count the total number of passengers for this booking
            $package->total_people = $group->count();
            // Add the package to the collection
            $packages->push($package);
        }

        $userIds = $packages->pluck('user_id')->unique();

        // Fetch the corresponding user names
        $users = User::whereIn('id', $userIds)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('id');

        // Return only the partial view for AJAX
        return view('admin.pending.partial.package', compact('packages', 'users'));
    }



    public function hotels()
    {
        $hotels = Hotel::where(['booking', 0])->get();  // Fetch hotel data
        return view('admin.booking.details', compact('hotels'))->with('type', 'hotels');
    }

    public function holidays()
    {
        $holidays = Holiday::where([['status', 0], ['is_deleted', 0]])->get();// Fetch holiday data
        return view('admin.booking.details', compact('holidays'))->with('type', 'holidays');
    }

    /**
     * Show the payment status page.
     */
    public function payment()
    {
        $pendingPayments = Payments::where('is_accepted', 0)
            ->with('cardDetails') // Eager load the card details to avoid N+1 query problem
            ->with('user')
            ->get();

        return view('admin.payments.index', compact('pendingPayments'));
    }

    public function approve(Request $request)
    {
        // Find the payment by ID
        $payment = Payments::findOrFail($request->payment_id);

        // Set the is_accepted field to 1
        $payment->is_accepted = 1;
        $payment->save();

        // Return success message
        return redirect()->back()->with('success', 'Payment approved successfully');
    }

    /**
     * Accept the payment and process it.
     **/
    public function accept(Request $request)
    {
        if ($request->input('accepting_mode') == '1') {
            // Process payment acceptance
            $payment = Payments::findOrFail($request->input('payment_id'));

            $cardDetails = new CardDetails();
            $cardDetails->user_id = $payment->user_id;
            $cardDetails->card_number = $request->input('card_number');
            $cardDetails->card_holder_name = $request->input('card_holder_name');
            $cardDetails->expiry_month = $request->input('expiry_month');
            $cardDetails->expiry_year = $request->input('expiry_year');
            $cardDetails->cvv = $request->input('cvv');
            $cardDetails->billing_address = $request->input('billing_address');
            $cardDetails->save();

            $payment->is_accepted = 1;
            $payment->save();

            return redirect()->route('admin.payments-details')->with('success', 'Payment accepted.');
        } else {
            dd($request);
            // Handle pending payments or other logic
            return redirect()->route('admin.payments-details')->with('info', 'No payments were accepted Please Contact provider.');
        }
    }


    /**
     * Reject the payment and store card details.
     **/
    public function reject(Request $request)
    {
        // Store card details and update payment status
        $payment = Payments::findOrFail($request->input('payment_id'));

        $cardDetails = new CardDetails();
        $cardDetails->user_id = $payment->user_id;
        $cardDetails->card_number = $request->input('card_number');
        $cardDetails->card_holder_name = $request->input('card_holder_name');
        $cardDetails->expiry_month = $request->input('expiry_month');
        $cardDetails->expiry_year = $request->input('expiry_year');
        $cardDetails->cvv = $request->input('cvv');
        $cardDetails->billing_address = $request->input('billing_address');
        $cardDetails->save();

        $payment->card_id = $cardDetails->id;
        $payment->save();

        return redirect()->route('admin.payments-details')->with('success', 'Payment rejected and card details saved.');
    }


    public function loadPending($type)
    {
        switch ($type) {
            case 'packages':
                $packages = Package::where('is_deleted', 0)->get();
                return view('admin.pending.partial.package', compact('packages'));
            case 'flights':
                // Assuming you have a Flight model and logic to get pending flights
                $flights = Flight::where('status', 'pending')->get();
                return view('admin.pending.partial.flight', compact('flights'));
            case 'hotels':
                // Assuming you have a Hotel model and logic to get pending hotels
                $hotels = Hotel::where('status', 'pending')->get();
                return view('admin.pending.partial.hotels', compact('hotels'));
            case 'holidays':
                // Assuming you have a Holiday model and logic to get pending holidays
                $holidays = Holiday::where('status', 'pending')->get();
                return view('admin.pending.partial.holiday', compact('holidays'));
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }


}

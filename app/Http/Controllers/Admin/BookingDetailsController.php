<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\Package;
use App\Models\Holiday;
use App\Models\Hotel;
use App\Models\Payments;
use App\Models\User;

class BookingDetailsController extends Controller
{
    public function index()
    {
        return view('admin.booking.index');
    }

    // public function packages()
    // {
    //     $packages = Package::where(['booking',1])->get();  // Fetch package data
    //     return view('admin.booking.details', compact('packages'))->with('type', 'packages');
    // }

    public function flights()
    {
        $flights = Flight::where([['status', 1], ['is_deleted', 0]])->get();
        $userIds = $flights->pluck('created_by')->unique();

        // Fetch the corresponding user names
        $users = User::whereIn('id', $userIds)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('id');
        // Return only the partial view for AJAX
        return view('admin.booking.partial.flight', compact('flights', 'users'));
    }

    public function packages()
    {
        $packages = Package::where([['status', 1], ['is_deleted', 0]])->get();
        $userIds = $packages->pluck('user_id')->unique();
        // dd($packages);
        // Fetch the corresponding user names
        $users = User::whereIn('id', $userIds)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('id');
        // Return only the partial view for AJAX
        return view('admin.booking.partial.package', compact('packages', 'users'));
    }

    public function hotels()
    {
        $hotels = Payments::where(['category',3])->get();  // Fetch hotel data
        return view('admin.booking.partial.hotels', compact('hotels'))->with('type', 'hotels');
    }

    public function holidays()
    {
        $holidays = Payments::where(['category',4])->get();  // Fetch holiday data
        return view('admin.booking.partial.holiday', compact('holidays'))->with('type', 'holidays');
    }


}

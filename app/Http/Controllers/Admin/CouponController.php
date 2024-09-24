<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where([
            ['active', 1],
            ['is_deleted', 0],
            ['is_expired', 0]
        ])->get();
        return view('admin.coupon.index', compact('coupons'));
    }

    public function create()
    {
        if (Auth::user()->user_type == 1 || Auth::user()->user_type == 2) {
            return view('admin.coupon.create');
        } else {
            return redirect()->back()->withErrors(['error' => 'You are not authorized.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|unique:coupons',
            'discount' => 'required|numeric',
            'active' => 'required|boolean',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->coupon_code = strtoupper($request->coupon_code);
        $coupon->discount = $request->discount;
        $coupon->active = $request->active;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->created_by = Auth::user()->id;
        $coupon->save();

        return redirect('/admin/coupon')->with('success', 'Coupon created successfully');
    }

    public function edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'coupon_code' => 'required|unique:coupons,coupon_code,' . $id,
            'discount' => 'required|numeric',
            'active' => 'required|boolean',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($id);
        $coupon->coupon_code = $request->coupon_code;
        $coupon->discount = $request->discount;
        $coupon->active = $request->active;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->updated_by = Auth::user()->id;
        $coupon->save();

        return redirect('/admin/coupon')->with('success', 'Coupon updated successfully');
    }

    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            $coupon->update([
                'is_deleted' => 1,
                'deleted_at' => now()
            ]);
            return redirect('/admin/coupon')->with('success', 'Coupon moved to Trash Successfully!');
        }
        return redirect('/admin/coupon')->with('error', 'Coupon not found!');
    }

    public function trash()
    {
        $trashed_coupons = Coupon::where('is_deleted', 1)->get();
        return view('admin.coupon.trash', compact('trashed_coupons'));
    }

    public function restore($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            $coupon->update([
                'is_deleted' => 0,
                'deleted_at' => null
            ]);
            return redirect('/admin/coupon/trash')->with('success', 'Coupon restored successfully!');
        }
        return redirect('/admin/coupon/trash')->with('error', 'Coupon not found!');
    }

    public function permanentDelete($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            $coupon->delete();
            return redirect('/admin/coupon/trash')->with('success', 'Coupon deleted permanently!');
        }
        return redirect('/admin/coupon/trash')->with('error', 'Coupon not found!');
    }
    public function checkExpiredCoupons()
    {
        $today = now()->format('Y-m-d');

        $expiredCoupons = Coupon::where('expiry_date', '<', $today)
            ->where('is_expired', 0) // Check if already marked as expired
            ->get();

        foreach ($expiredCoupons as $coupon) {
            $coupon->update([
                'is_expired' => 1,
                'is_deleted' => 1, // Move to trash
            ]);
        }
    }
}

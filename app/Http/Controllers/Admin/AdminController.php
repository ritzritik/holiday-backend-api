<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function profile()
    {
        $user = Auth::guard('admin')->user();
        return view('admin.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'nullable|string|max:15',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_pic')) {
            // Handle file upload
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $user->profile_pic = $path;
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->save();

        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function getRegisteredUser()
    {
        $regis_users = User::all();
        return view('admin.registereduser', compact('regis_users'));
    }
}

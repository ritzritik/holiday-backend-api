<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function profile()
    {
        // return redirect('admin.dashboard');
        return view('admin.dashboard');
    }

    public function getRegisteredUser()
    {
        $regis_users = User::where('user_type',0)->get();
        return view('admin.registereduser', compact('regis_users'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuthUser;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */

    //  public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    //     $this->middleware('auth')->only('logout');
    // }

    // protected function authenticated(Request $request, $user)
    // {
    //     if (Auth::user()->user_type == 1) {
    //         return redirect('/dashboard');
    //     }
    //     return redirect()->login();

    //     // Redirect to the default location for non-admins
    //     // return redirect('/home');
    // }

     public function showAdminLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle the admin login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

     public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)
            ->whereIn('user_type', [1, 2])
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->get('remember'));
            return view('admin.dashboard')->with('message', 'You are logged in as Admin.');
            // return redirect()->route('admin.dashboard')->with('message', 'You are logged in as Admin.');
        } else {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
    }


    /**
     * Handle the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'roll' => 0], $request->get('remember'))) {
            return redirect()->route('user.dashboard')->with('message', 'You are logged in as User.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}

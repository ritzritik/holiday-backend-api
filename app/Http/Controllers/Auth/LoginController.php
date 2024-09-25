<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard'); // Redirect to admin dashboard if already logged in
        }
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

        // Retrieve credentials
        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check user type and redirect accordingly
            switch ($user->user_type) {
                case 1:
                    return redirect()->route('admin.dashboard')->with('message', 'You are logged in as Admin.');
                case 2:
                    return redirect()->route('manager.dashboard')->with('message', 'You are logged in as Manager.');
                case 3:
                    return redirect()->route('editor.dashboard')->with('message', 'You are logged in as Editor.');
                default:
                    Auth::logout(); // Log out if user_type is unexpected
                    return back()->withErrors(['email' => 'Unauthorized access.']);
            }
        } else {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Log the user out

        // Optionally, invalidate the user's session
        $request->session()->invalidate();

        // Regenerate the CSRF token to prevent session fixation
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Successfully logged out']);
    }

    //composer require tymon/jwt-auth
    //php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
    //php artisan jwt:secret
    // Set Up Auth Configuration: Update your config/auth.php file to include a new guard for API:

    //     php
    //     Copy code
    //     'guards' => [
    //         'api' => [
    //             'driver' => 'jwt',
    //             'provider' => 'users',
    //         ],
    //     ],

    //Create the Authentication Controller: Create a new controller to handle login and token generation.

    // php
    // Copy code
    // php artisan make:controller AuthController

    //In AuthController, implement the login method:

    // php
    // Copy code
    // use Tymon\JWTAuth\Facades\JWTAuth;
    // use Illuminate\Support\Facades\Auth;

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     $credentials = $request->only('email', 'password');

    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         return response()->json(['error' => 'invalid_credentials'], 401);
    //     }

    //     return response()->json(compact('token'));
    // }
    // Define API Routes: In your routes/api.php, set up the route for login:

    // php
    // Copy code
    // Route::post('/login', [AuthController::class, 'login']);
    // Modify Your API URL: Make sure your API URL points to the login endpoint:

    //     javascript
    //     Copy code
    //     const API_URL = 'http://localhost:8000/api/login';
    //     Login Function: Update your login function to handle the JWT response:

    //     javascript
    //     Copy code
    //     export const login = async (email, password) => {
    //       try {
    //         const response = await fetch(API_URL, {
    //           method: 'POST',
    //           headers: {
    //             'Content-Type': 'application/json',
    //           },
    //           body: JSON.stringify({ email, password }),
    //         });

    //         if (!response.ok) {
    //           throw new Error('Login failed');
    //         }

    //         const data = await response.json();
    //         setToken(data.token); // Store the JWT token
    //         return true; // Login successful
    //       } catch (error) {
    //         console.error('Authentication failed:', error);
    //         return false; // Login failed
    //       }
    //     };
    //     Session Management: You already have functions to manage the token in localStorage. This allows your application to maintain sessions smoothly:

    //     setToken(token): Store the JWT token.
    //     removeToken(): Clear the token on logout.
    //     isAuthenticated(): Check if a user is authenticated.
    //     Middleware for Protected Routes
    //     You may want to protect certain routes in your Laravel backend. To do this, create a middleware that checks for a valid JWT token.

    //     Apply Middleware: In your routes/api.php, apply the middleware to routes you want to protect:

    //     php
    //     Copy code
    //     Route::middleware('auth:api')->group(function () {
    //         // Protected routes here
    //     });
    //     Use Middleware in Controller: You can also use the middleware in your controllers to restrict access.

    //     Handling Sessions in React
    //     To manage sessions effectively:

    //     Use localStorage to store the JWT.
    //     Set up an Axios instance (or fetch) to automatically include the token in the headers for API requests:
    //     javascript
    //     Copy code
    //     const apiClient = axios.create({
    //       baseURL: 'http://localhost:8000/api',
    //     });

    //     apiClient.interceptors.request.use((config) => {
    //       const token = getToken();
    //       if (token) {
    //         config.headers['Authorization'] = `Bearer ${token}`;
    //       }
    //       return config;
    //     });
}

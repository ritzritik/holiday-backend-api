<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Register a new user (signup).
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', //confirmed
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number ?? null,
            'email' => $request->email,
            'username' => $request->username ?? null, 
            'bio' => $request->bio ?? null,
            'profile_photo' => $request->profile_photo ?? null, 
            'is_active' => 1,
            'is_deleted' => 0, 
            'password' => Hash::make($request->password),
        ]);

        // Generate JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Return success response with token
        return response()->json([
            'message' => 'User successfully registered',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * Authenticate user (login).
     */
    public function login(Request $request)
    {
        // Validate login credentials
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        try {
            // Attempt to authenticate the user and generate a token
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    
        // Manually retrieve the user
        $user = User::where('email', $request->email)->first();
    
        // Check if user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Return success response with token and user
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    

    /**
     * Log the user out (invalidate the token).
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'User successfully logged out',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to logout, please try again',
            ], 500);
        }
    }

    /**
     * Get authenticated user details.
     */
    public function me()
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }
}

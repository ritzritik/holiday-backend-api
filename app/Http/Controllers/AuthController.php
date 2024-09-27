<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserBooking;
use App\Models\UserVoucher;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\JsonResponse;
use App\Models\ContactAdmin;

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
        $token = JWTAuth::fromUser($user, ['exp' => now()->addMinutes(60)->timestamp]);
        $user->profile_photo = $this->getProfilePhotoUrl($user->profile_photo);
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
            // Attempt to authenticate the user and generate a token with custom expiration time
            if (!$token = JWTAuth::attempt($credentials, ['exp' => now()->addMinutes(60)->timestamp])) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    
        // Manually retrieve the user
        $user = User::where('email', $request->email)->first();
        $user->profile_photo = $this->getProfilePhotoUrl($user->profile_photo);
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
    
    private function getProfilePhotoUrl($profilePhoto)
    {
        if ($profilePhoto) {
            return url('uploads/profile_photos/' . $profilePhoto); 
        }
        return null;
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
     * Update user profile.
     */
    public function profile_update(Request $request)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
    
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = User::find(auth('api')->id());
    
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
            'bio' => 'nullable|string',
            'phone_number' => 'nullable|string|max:15',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        // Update profile information
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
    
        // Handle profile photo upload and resizing
        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo');
    
            // Resize and compress image manually
            $resizedImage = $this->resizeImage($profilePhoto, 100); // Resize to max 100KB
    
            // Generate unique file name
            $filename = time() . '_' . $user->id . '.' . $profilePhoto->getClientOriginalExtension();
    
            // Ensure the directory exists
            $uploadDirectory = public_path('uploads/profile_photos/');
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true); // Create the directory if it doesn't exist
            }
    
            // Save the resized image in the public/uploads/profile_photos directory
            $path = $uploadDirectory . $filename;
            imagejpeg($resizedImage, $path); // Save the image
    
            // Update user profile photo path in the database
            $user->profile_photo = $filename;
        }
    
        // Save the updated user profile
        $user->save();
    
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ], 200);
    }
    

    /**
     * Resize image to ensure it's between 50-100KB.
     *
     * @param  \Illuminate\Http\UploadedFile $image
     * @param  int $maxSizeKB
     * @return \GdImage
     */
    private function resizeImage($image, $maxSizeKB): \GdImage
    {
        // Create a new image resource from the uploaded file
        $originalImage = imagecreatefromstring(file_get_contents($image));

        if (!$originalImage) {
            throw new \Exception('Could not create image from file');
        }

        $width = imagesx($originalImage);
        $height = imagesy($originalImage);

        // Start at 100% size
        $resizePercentage = 100;

        do {
            // Calculate new dimensions
            $newWidth = intval($width * ($resizePercentage / 100));
            $newHeight = intval($height * ($resizePercentage / 100));

            // Create a new true color image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Output the resized image to a buffer and get its size
            ob_start();
            imagejpeg($resizedImage, null, 75); // Encode to JPEG with 75% quality
            $imageData = ob_get_contents();
            ob_end_clean();

            // Calculate the size of the image in kilobytes
            $imageSizeKB = strlen($imageData) / 1024;

            // Free memory
            imagedestroy($resizedImage);

            // Reduce size further if it exceeds the max size
            $resizePercentage -= 10; // Reduce by 10% each time

        } while ($imageSizeKB > $maxSizeKB && $resizePercentage > 10);

        // Finally create the resized image
        $finalImage = imagecreatefromstring($imageData);

        // Free memory
        imagedestroy($originalImage);

        return $finalImage; // Return the resized image resource
    }


    public function all_bookings(Request $request) {
        // Get the Authorization token from the request header
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
    
        // Authenticate the user
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Fetch all bookings for the authenticated user
        $bookings = UserBooking::where('user_id', $user->id)->get();
    
        // Fetch all vouchers for the authenticated user
        $vouchers = UserVoucher::where('user_id', $user->id)->get();
    
        // Return success response with the user's bookings and vouchers
        return response()->json([
            'message' => 'success',
            'data' => [
                'bookings' => $bookings,
                'vouchers' => $vouchers
            ],
            'statusCode' => 200
        ]);
    }

    public function contact_admin(Request $request) : JsonResponse
    {
        // Get the token and authenticate user
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
    
        $user = auth('api')->user(); // Authenticate the user using the token
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
    
        // Save the contact request to the database
        $contact = ContactAdmin::create([
            'user_id' => $user->id,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'message' => $validatedData['message'],
        ]);
    
        return response()->json([
            'message' => 'Contact request submitted successfully',
            'statusCode' => 200,
            'contact' => $contact
        ]);
    }
    
    

    /**
     * Delete user profile.
     */
    public function profile_delete()
    {
        $user = auth('api')->user();

        // If auth('api')->user() fails, try to retrieve manually
        if (!$user) {
            $user = User::find(auth('api')->id());

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
        }

        // Option 1: Hard delete the user (remove from database)
        // $user->delete();

        // Option 2: Soft delete (set a flag, e.g., 'is_deleted' field)
        $user->is_deleted = 1;
        $user->save();

        // Optionally, you can invalidate the token after deletion
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Profile deleted successfully',
        ], 200);
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;


class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect(); // stateless for API
    }


    public function handleGoogleCallback(Request $request)
    {
        try {
            // Retrieve user from Google via Socialite
            $googleUser = Socialite::driver('google')->stateless()->user(); // Stateless for API
    
            // Check if the user exists in the database, or create a new one
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                ]
            );
    
            // Log the user in
            Auth::login($user);
    
            // Generate a personal access token
            $token = $user->createToken('user-token')->plainTextToken;
    
            // Send the encrypted token to the frontend (React)
            return redirect()->to(env('APP_FRONTEND_URL') . "/auth/callback?token=" . $token);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong, please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function encryptData($data)
{
    // Get the APP_KEY from the environment
    $key = env('APP_KEY'); // The APP_KEY should be 32 bytes for AES-256
    if (strlen($key) !== 32) {
        throw new \Exception('The APP_KEY should be 32 bytes for AES-256');
    }

    // Generate a 12-byte nonce (IV) for AES-GCM encryption
    $iv = random_bytes(12);

    // The cipher method used (AES-256-GCM)
    $cipher = 'aes-256-gcm';

    // Encrypt the data using openssl_encrypt
    $ciphertext = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

    // Check for encryption failure
    if ($ciphertext === false) {
        throw new \Exception('Encryption failed.');
    }

    // Return the IV, ciphertext, and authentication tag encoded in base64
    return base64_encode($iv . $ciphertext . $tag);
}
    
    

    
    

    public function logoutUser(Request $request)
    {
        try {
            // Revoke the current user's token
            $request->user()->currentAccessToken()->delete();
    
            return response()->json([
                'message' => 'User logged out successfully',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong, please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logoutAdmin(Request $request)
{
    try {
        // Revoke the current admin's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Admin logged out successfully',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong, please try again.',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        $admin = Admin::where('email', $request->email)->first();
    
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    
        // ✅ Generate an API token
        $token = $admin->createToken('admin-token')->plainTextToken;
    
        return response()->json([
            'message' => 'Admin logged in successfully',
            'token' => $token // ✅ Return the token
        ]);
    }
    
}

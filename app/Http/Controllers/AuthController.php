<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect(); // stateless for API
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user(); // stateless for API
            
            // Check if the user exists in the database
            $user = User::where('email', $googleUser->getEmail())->first();
    
            // If user doesn't exist, create a new user
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'oauth_id' => $googleUser->id,
                ]);
            }

            // Log the user in without using sessions
            Auth::login($user);

            // Generate a personal access token
            $token = $user->createToken('GoogleOAuth')->plainTextToken;

            // Return a JSON response with the user and token
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong, please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    public function logout()
    {
        Auth::logout();
        return redirect('/');
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

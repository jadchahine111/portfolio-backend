<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // You can remove stateless() if you don't need it
            $googleUser = Socialite::driver('google')->user();
    
    
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
            
            Auth::login($user);

            // Return a JSON response or redirect to another page
            return response()->json([
                'user' => $user,
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
}

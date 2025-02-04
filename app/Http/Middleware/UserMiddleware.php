<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request has a valid token for user
        if ($request->bearerToken()) {
            // Attempt to authenticate the token as a user
            $user = Auth::guard('api')->user(); // Use API guard for user

            // If user is not authenticated, return unauthorized response
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}

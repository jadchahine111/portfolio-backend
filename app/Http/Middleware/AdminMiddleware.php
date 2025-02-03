<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request has a valid token and it is an admin token
        if ($request->bearerToken()) {
            // Attempt to authenticate the token as an admin
            $user = Auth::guard('admin')->user(); // Use admin guard

            // If the user is not authenticated or not an admin, return unauthorized response
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}

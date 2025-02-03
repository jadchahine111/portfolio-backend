<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

   
Route::middleware('auth:sanctum')->get('/admin/me1', function (Request $request) {
    return response()->json([
        'message' => 'Authenticated Admin',
        'admin' => $request->user()
    ]);
});
Route::post('admin/login', [AuthController::class, 'login']);

// Redirect to Google authentication
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);

// Google callback route
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
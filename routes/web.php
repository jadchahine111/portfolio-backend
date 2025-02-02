<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect to Google authentication
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);

// Google callback route
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

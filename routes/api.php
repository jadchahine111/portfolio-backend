<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubcategoriesController;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;

// Admin Authentication
Route::post('admin/login', [AuthController::class, 'login']);

// User Authentication
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);


// Admin routes
Route::middleware([AdminMiddleware::class])->group(function () {
        // Logout 
        Route::post('/logoutAdmin', [AuthController::class, 'logoutAdmin']);

        // CRUD Categories
        Route::post('/admin/categories', [CategoriesController::class, 'addCategory']);
        Route::put('/admin/categories/{id}', [CategoriesController::class, 'updateCategory']);
        Route::get('/admin/categories', [CategoriesController::class, 'viewCategories']);
        Route::delete('/admin/categories/{id}', [CategoriesController::class, 'deleteCategory']);
    
        // CRUD Subcategories
        Route::post('/admin/subcategories', [SubcategoriesController::class, 'addSubcategory']);
        Route::put('/admin/subcategories/{id}', [SubcategoriesController::class, 'updateSubcategory']);
        Route::get('/admin/subcategories', [SubcategoriesController::class, 'viewSubcategories']);
        Route::delete('/admin/subcategories/{id}', [SubcategoriesController::class, 'deleteSubcategory']);
    
        // CRUD Blogs
        Route::post('/admin/blogs', [BlogController::class, 'addBlog']);
        Route::put('/admin/blogs/{id}', [BlogController::class, 'updateBlog']);
        Route::get('/admin/blogs/{id}', [BlogController::class, 'viewBlogById']);
        Route::delete('/admin/blogs/{id}', [BlogController::class, 'deleteBlog']);


});

Route::get('/blogs', [BlogController::class, 'viewBlogs']);
Route::get('/categories', [CategoriesController::class, 'viewCategories']);




// User Routes
Route::middleware([UserMiddleware::class])->group(function () {

    Route::get('/session', [AuthController::class, 'getSessionData']);

    // Logout 
    Route::post('/logoutUser', [AuthController::class, 'logoutUser']);

    // Like / Unlike Blogs
    Route::post('/blogs/{blogId}/like-toggle', [LikeController::class, 'toggleLike']);

    // Like / Unlike Reviews
    Route::post('/reviews/{reviewId}/like-toggle', [LikeController::class, 'toggleLike']);

    Route::get('/user/blogs', [BlogController::class, 'viewBlogs']);

    // Like / Unlike Replies
    Route::post('/replies/{replyId}/like-toggle', [LikeController::class, 'toggleLike']);

    // Add / Delete Reviews
    Route::post('/blogs/{blogId}/reviews', [ReviewController::class, 'addReview']);
    Route::delete('/reviews/{reviewId}', [ReviewController::class, 'deleteReview']);

    // Reply to Review / Delete Reply
    Route::post('/reviews/{reviewId}/reply', [ReviewController::class, 'replyToReview']);
    Route::delete('/replies/{replyId}', [ReviewController::class, 'deleteReply']);

});

Route::get('/generate-key', function () {
    // Generate a 256-bit key (32 bytes)
    $key = openssl_random_pseudo_bytes(32);

    // Convert the key to hexadecimal format
    $hexKey = bin2hex($key);

    // Return the key as a JSON response
    return response()->json([
        'key' => $hexKey
    ]);
});
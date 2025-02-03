<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Admin Authentication
Route::post('admin/login', [AuthController::class, 'login']);

// User Authentication
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);


// Admin routes
Route::middleware(['auth:admin', 'admin'])->group(function () {
        // Logout 
        Route::post('/logoutAdmin', [AuthController::class, 'logoutAdmin']);

        // CRUD Categories
        Route::post('/admin/categories', [AdminController::class, 'addCategory']);
        Route::put('/admin/categories/{id}', [AdminController::class, 'updateCategory']);
        Route::get('/admin/categories', [AdminController::class, 'viewCategories']);
        Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory']);
    
        // CRUD Subcategories
        Route::post('/admin/subcategories', [AdminController::class, 'addSubcategory']);
        Route::put('/admin/subcategories/{id}', [AdminController::class, 'updateSubcategory']);
        Route::get('/admin/subcategories', [AdminController::class, 'viewSubcategories']);
        Route::delete('/admin/subcategories/{id}', [AdminController::class, 'deleteSubcategory']);
    
        // CRUD Blogs
        Route::post('/admin/blogs', [AdminController::class, 'addBlog']);
        Route::put('/admin/blogs/{id}', [AdminController::class, 'updateBlog']);
        Route::get('/admin/blogs', [AdminController::class, 'viewBlogs']);
        Route::get('/admin/blogs/{id}', [AdminController::class, 'viewBlogById']);
        Route::delete('/admin/blogs/{id}', [AdminController::class, 'deleteBlog']);


});



// User Routes
Route::middleware(['auth:api', 'user'])->group(function () {

    // Logout 
    Route::post('/logoutUser', [AuthController::class, 'logoutUser']);

    // Like / Unlike Blogs
    Route::post('/blogs/{blogId}/like', [LikeController::class, 'likeBlog']);
    Route::delete('/blogs/{blogId}/unlike', [LikeController::class, 'unlikeBlog']);

    // Like / Unlike Reviews
    Route::post('/reviews/{reviewId}/like', [LikeController::class, 'likeReview']);
    Route::delete('/reviews/{reviewId}/unlike', [LikeController::class, 'unlikeReview']);

    // Like / Unlike Replies
    Route::post('/replies/{replyId}/like', [LikeController::class, 'likeReply']);
    Route::delete('/replies/{replyId}/unlike', [LikeController::class, 'unlikeReply']);

    // Add / Delete Reviews
    Route::post('/blogs/{blogId}/reviews', [ReviewController::class, 'addReview']);
    Route::delete('/reviews/{reviewId}', [ReviewController::class, 'deleteReview']);

    // Reply to Review / Delete Reply
    Route::post('/reviews/{reviewId}/reply', [ReviewController::class, 'replyToReview']);
    Route::delete('/replies/{replyId}', [ReviewController::class, 'deleteReply']);

});

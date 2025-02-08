<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Like;
use App\Models\Review;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserActivities($userId, $blogId)
    {
        // Fetch the blog
        $blog = Blog::findOrFail($blogId);

        // Check if the user liked the blog
        $blogLiked = Like::where('user_id', $userId)
                        ->where('likeable_type', 'App\Models\Blog')
                        ->where('likeable_id', $blogId)
                        ->exists();

        // Get reviews for the blog
        $reviews = $blog->reviews;

        // Get the reviews that the user has liked
        $likedReviews = $reviews->filter(function ($review) use ($userId) {
            return Like::where('user_id', $userId)
                        ->where('likeable_type', 'App\Models\Review')
                        ->where('likeable_id', $review->id)
                        ->exists();
        });

        // Return all user activities
        return response()->json([
            'blog_liked' => $blogLiked, // Boolean indicating if the user liked the blog
            'reviews' => $reviews, // All reviews associated with the blog
            'liked_reviews' => $likedReviews->pluck('id'), // Only the ids of the reviews the user liked
        ]);
    }
}

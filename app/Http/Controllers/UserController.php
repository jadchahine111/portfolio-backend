<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Like;
use App\Models\Review;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserActivities($blogId, Request $request)
    {
        // Retrieve the authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Fetch the blog
        $blog = Blog::findOrFail($blogId);

        // Check if the user liked the blog
        $blogLiked = Like::where('user_id', $user->id)
                        ->where('likeable_type', Blog::class)
                        ->where('likeable_id', $blogId)
                        ->exists();

        // Get reviews for the blog
        $reviews = $blog->reviews;

        // Get the IDs of reviews the user has liked
        $likedReviewIds = Like::where('user_id', $user->id)
            ->where('likeable_type', Review::class)
            ->whereIn('likeable_id', $reviews->pluck('id'))
            ->pluck('likeable_id');

        // Return all user activities
        return response()->json([
            'blog_liked' => $blogLiked, // Boolean indicating if the user liked the blog
            'liked_reviews' => $likedReviewIds, // Array of review IDs the user liked
        ]);
    }
}

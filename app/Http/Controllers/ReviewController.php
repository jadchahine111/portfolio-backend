<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Blog;

class ReviewController extends Controller
{
    // Add a review to a blog
    public function addReview(Request $request, $blogId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $blog = Blog::findOrFail($blogId);

        $review = $blog->reviews()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Review added', 'review' => $review], 201);
    }

    // Delete a review
    public function deleteReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        // Ensure the user is authorized to delete (Admin or Review Owner)
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete(); // Delete the review

        return response()->json(['message' => 'Review deleted'], 200);
    }

    // Get reviews for a blog
    public function getReviewsForBlog($blogId)
    {
        $blog = Blog::findOrFail($blogId);

        // Eager load relationships: 'user' for the reviewer, and 'likes' for the review's likes
        $reviews = $blog->reviews()
                        ->with(['user', 'likes']) // Removed 'replies.user' and 'replies.likes'
                        ->get();

        // Transform the data for each review
        $data = $reviews->map(function ($review) {
            return [
                'id'             => $review->id,
                'content'        => $review->content,
                'reviewer_email' => $review->user ? $review->user->email : null,
                'likes_count'    => $review->likes->count(),
                'created_at'     => $review->created_at,
            ];
        });

        // Calculate the total reviews count
        $totalCommentsForBlog = $reviews->count(); // Only count reviews

        return response()->json([
            'reviews' => $data,
            'total_comments' => $totalCommentsForBlog, // Total reviews count
        ], 200);
    }
}

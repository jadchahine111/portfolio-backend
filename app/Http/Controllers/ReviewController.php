<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Reply;
use App\Models\Blog;

class ReviewController extends Controller
{
    //  Add a review to a blog
    public function addReview(Request $request, $blogId)
    {
    $request->validate([
        'content' => 'required|string',
    ]);

    $review = new Review();
    $review->user_id = auth()->id();
    $review->content = $request->content;
    $review->blog_id = $blogId;
    $review->save();

    return response()->json([
        'message' => 'Review added',
        'review' => [
            'id' => $review->id,
            'user_id' => $review->user_id,
            'user_email' => $review->user->email, // Add user's email
            'user_name' => $review->user->name, // Add user's name
            'content' => $review->content,
            'blog_id' => $review->blog_id,
            'created_at' => $review->created_at,
            'updated_at' => $review->updated_at,
        ]
    ], 201);
    }


    // Delete a review and its replies
    public function deleteReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        // Ensure the user is authorized to delete (Admin or Review Owner)
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->replies()->delete(); // Delete all replies first
        $review->delete(); // Then delete the review

        return response()->json(['message' => 'Review and its replies deleted'], 200);
    }

    // Reply to a review
    public function replyToReview(Request $request, $reviewId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $review = Review::findOrFail($reviewId);

        $reply = $review->replies()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Reply added', 'reply' => $reply], 201);
    }

    // Delete a reply
    public function deleteReply($replyId)
    {
        $reply = Reply::findOrFail($replyId);

        // Ensure the user is authorized to delete (Admin or Reply Owner)
        if (auth()->id() !== $reply->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->delete();

        return response()->json(['message' => 'Reply deleted'], 200);
    }

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

    // Calculate the total comments (only reviews, no replies)
    $totalCommentsForBlog = $reviews->count(); // Only count reviews

    return response()->json([
        'reviews' => $data,
        'total_comments' => $totalCommentsForBlog, // Total reviews count
    ], 200);
}


    
    
}

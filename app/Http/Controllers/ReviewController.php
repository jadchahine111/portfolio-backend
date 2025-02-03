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

        $blog = Blog::findOrFail($blogId);

        $review = $blog->reviews()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Review added', 'review' => $review], 201);
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
}

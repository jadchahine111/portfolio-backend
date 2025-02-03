<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Blog;
use App\Models\Review;
use App\Models\Reply;

class LikeController extends Controller
{
    // Like a blog
    public function likeBlog($blogId)
    {
        return $this->like(Blog::class, $blogId);
    }

    //  Unlike a blog
    public function unlikeBlog($blogId)
    {
        return $this->unlike(Blog::class, $blogId);
    }

    //  Like a review
    public function likeReview($reviewId)
    {
        return $this->like(Review::class, $reviewId);
    }

    //  Unlike a review
    public function unlikeReview($reviewId)
    {
        return $this->unlike(Review::class, $reviewId);
    }

    //  Like a reply
    public function likeReply($replyId)
    {
        return $this->like(Reply::class, $replyId);
    }

    //  Unlike a reply
    public function unlikeReply($replyId)
    {
        return $this->unlike(Reply::class, $replyId);
    }

    //  Generic Like function
    private function like($model, $id)
    {
        $entity = $model::findOrFail($id);

        if (!$entity->likes()->where('user_id', auth()->id())->exists()) {
            $entity->likes()->create(['user_id' => auth()->id()]);
            return response()->json(['message' => 'Liked successfully'], 200);
        }

        return response()->json(['message' => 'Already liked'], 400);
    }

    // Generic Unlike function
    private function unlike($model, $id)
    {
        $entity = $model::findOrFail($id);

        $like = $entity->likes()->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Unliked successfully'], 200);
        }

        return response()->json(['message' => 'Not liked yet'], 400);
    }
}

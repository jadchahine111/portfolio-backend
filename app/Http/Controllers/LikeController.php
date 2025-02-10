<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Blog;
use App\Models\Review;

class LikeController extends Controller
{
    public function toggleLike(Request $request, $blogId, $reviewId = null)
    {
        $userId = auth()->id();
        $modelClass = $reviewId ? Review::class : Blog::class;
        $entityId = $reviewId ?? $blogId;

        // Check if the like already exists
        $existingLike = Like::where('user_id', $userId)
            ->where('likeable_id', $entityId)
            ->where('likeable_type', $modelClass)
            ->exists();

        if ($existingLike) {
            // Unlike (delete directly)
            Like::where('user_id', $userId)
                ->where('likeable_id', $entityId)
                ->where('likeable_type', $modelClass)
                ->delete();

            return response()->json(['message' => 'Unliked successfully', 'liked' => false], 200);
        }

        // Like (insert without loading the entity)
        Like::create([
            'user_id' => $userId,
            'likeable_id' => $entityId,
            'likeable_type' => $modelClass,
        ]);

        return response()->json(['message' => 'Liked successfully', 'liked' => true], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Blog;
use App\Models\Review;
use App\Models\Reply;

class LikeController extends Controller
{
    public function toggleLike(Request $request, $id)
    {
        $modelType = $request->route()->getName(); // Get the route name to determine the model
        $modelClass = $this->getModelClass($modelType);

        if (!$modelClass) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $entity = $modelClass::findOrFail($id);
        $userId = auth()->id();

        $like = $entity->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Unliked successfully', 'liked' => false], 200);
        } else {
            $entity->likes()->create(['user_id' => $userId]);
            return response()->json(['message' => 'Liked successfully', 'liked' => true], 200);
        }
    }

    private function getModelClass($routeName)
    {
        return match (true) {
            str_contains($routeName, 'blogs') => Blog::class,
            str_contains($routeName, 'reviews') => Review::class,
            str_contains($routeName, 'replies') => Reply::class,
            default => null
        };
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Like;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use Illuminate\Validation\ValidationException;
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

    public function sendEmail(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'message' => 'required|string',
            ]);

            $details = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'message' => $validatedData['message'],
            ];

            Mail::to('jad@jadchahine.me')->send(new ContactMail($details));

            return response()->json(['message' => 'Email sent successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}

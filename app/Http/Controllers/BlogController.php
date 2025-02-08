<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Review;
use App\Models\Blog;
use App\Models\Like;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function addBlog(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'date' => 'required|date',
            'subcategories' => 'required|array', // Subcategories should be an array
            'subcategories.*' => 'exists:subcategories,id', // Each subcategory ID must exist in the subcategories table
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create the blog
        $blog = Blog::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'date' => $request->date,
        ]);

        // Attach subcategories to the blog
        $blog->subcategories()->attach($request->subcategories);

        // Return the created blog and its associated subcategories
        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog->load('subcategories'), // Load subcategories with the blog
        ], 201);
    }

    public function viewBlogs()
    {
        // Eager load the related category and subcategories (directly from blogs)
        $blogs = Blog::with(['category', 'subcategories'])->get();
    
        // Transform each blog into your desired structure
        $transformedBlogs = $blogs->map(function ($blog) {
            return [
                'id'             => $blog->id,
                'title'          => $blog->title,
                'category_id'    => $blog->category_id, // from the blog model
                'excerpt'        => $blog->excerpt,
                'date'           => $blog->date,
                // Flatten the category into separate fields
                'category_name'  => $blog->category ? $blog->category->name : null,
                // Get subcategories directly from the blog
                'sub_categories' => $blog->subcategories->map(function ($subcategory) {
                    return [
                        'subcategory_id'   => $subcategory->id,
                        'subcategory_name' => $subcategory->name,
                    ];
                })->toArray(),
                'likes_count'    => $blog->likes->count(),
            ];
        });
    
        return response()->json([
            'blogs' => $transformedBlogs,
        ]);
    }

    public function deleteBlog($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }

    public function updateBlog(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'title' => 'sometimes|string|max:255',  // Title is optional during update
            'category_id' => 'sometimes|exists:categories,id',  // Category ID must exist in the categories table
            'excerpt' => 'sometimes|string',
            'content' => 'sometimes|string',
            'subcategories' => 'sometimes|array',  // Subcategories should be an array
            'subcategories.*' => 'exists:subcategories,id',  // Each subcategory ID must exist in the subcategories table
        ]);

        // Find the blog by ID
        $blog = Blog::find($id);

        // If the blog doesn't exist, return an error
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        // Update the blog's title, category, excerpt, and content (only if provided)
        if ($request->has('title')) {
            $blog->title = $request->title;
        }
        if ($request->has('category_id')) {
            $blog->category_id = $request->category_id;
        }
        if ($request->has('excerpt')) {
            $blog->excerpt = $request->excerpt;
        }
        if ($request->has('content')) {
            $blog->content = $request->content;
        }

        // Save the updated blog
        $blog->save();

        // Handle subcategory assignment and unassignment
        if ($request->has('subcategories')) {
            // Sync subcategories (this will add new subcategories and remove unlisted ones)
            $blog->subcategories()->sync($request->subcategories);
        }

        // Return the updated blog with subcategories
        return response()->json([
            'message' => 'Blog updated successfully',
            'blog' => $blog->load('subcategories')  // Return the updated blog with subcategories
        ], 200);
    }

    public function viewBlogById($id)
    {
        // Retrieve the blog by its ID, including its category, subcategories, and the number of likes
        $blog = Blog::with(['category', 'subcategories', 'likes'])->find($id);
        
        // If the blog is not found, return a 404 error
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        // Get the reviews for the blog
        $reviews = Review::with(['likes'])->where('blog_id', $id)->get();

        // Transform the blog data with the additional information you want
        $blogData = [
            'id'             => $blog->id,
            'title'          => $blog->title,
            'category_name'  => $blog->category ? $blog->category->name : null,
            'date'           => $blog->date,
            'content'        => $blog->content,
            'likes_count'    => $blog->likes->count(),
            'comments_count' => $reviews->count(),
            'reviews'        => $reviews->map(function ($review) {
                return [
                    'id'          => $review->id,
                    'user_name'   => $review->user->name,
                    'content'     => $review->content,
                    'created_at'  => $review->created_at,
                    'likes_count' => $review->likes->count(), // Add likes count for the review
                ];
            })->toArray(),
        ];

        return response()->json([
            'blog' => $blogData,
        ], 200);
    }
}

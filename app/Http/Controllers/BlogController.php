<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $blogs = Blog::all();
        return response()->json(['blogs' => $blogs]);
    }

    public function deleteBlog($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }

        public function viewBlogById($id)
        {
            // Retrieve the blog by its ID, including its category and subcategories
            $blog = Blog::with(['category', 'subcategories'])->find($id);
    
            // If the blog is not found, return a 404 error
            if (!$blog) {
                return response()->json(['error' => 'Blog not found'], 404);
            }
    
            // Return the blog along with its category and subcategories
            return response()->json([
                'blog' => $blog,
            ], 200);
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

}

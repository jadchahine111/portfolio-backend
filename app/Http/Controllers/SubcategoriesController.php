<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubcategoriesController extends Controller
{
    public function addSubcategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return response()->json(['subcategory' => $subcategory], 201);
    }

    public function updateSubcategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return response()->json(['subcategory' => $subcategory]);
    }

    public function viewSubcategories()
    {
        $subcategories = Subcategory::all();
        return response()->json(['subcategories' => $subcategories]);
    }

    public function deleteSubcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully']);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(category::all());
    }

    public function store(Request $request)
    {
        if (!auth()->user() || auth()->user()->is_admin != 1)  {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);
        $category = CategoryController::create([
            'name' => $request->name
        ]);
        return response()->json([
            'category' => $category,
            'message' => 'Category created'
        ]);
    }

    public function update(Request $request,$id)
    {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update($validated);

        return response()->json($category);

    }

    public function show(Request $request)
    {

    }

    public function delete($id)
    {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}

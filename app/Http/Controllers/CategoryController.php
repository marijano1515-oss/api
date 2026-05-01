<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\CategoryTranslations;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(
            category::query()
                ->with(['translations' => function ($query) {
                    $query->where('locale', app()->getLocale())
                        ->select('id', 'category_id', 'name');
                }])
                ->get()
        );
    }

    public function store(Request $request)
    {
        if (!auth()->user() || auth()->user()->is_admin != 1)  {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name_en' => 'required|string|max:255|unique:categories,name',
            'name_ka' => 'required|string|max:255',
        ]);
        $category = Category::create([
            'name' => $request->name_en
        ]);

        $category->translations()->create([
            'name' => $request->name_ka,
            'locale' => 'ka',

        ]);
        $category->translations()->create([
            'name' => $request->name_en,
            'locale' => 'en',

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

        return response()->json([
            'category' => $category,
            'message' => 'Category updated'
        ]);

    }

    public function show(Request $request,$id)
    {
        return response()->json(
            category::query()
                ->with(['translations'])
                ->find($id)
        );
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Deleted successfully']);

    }
}

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
                ->with('translations', function ($query) {
                    return $query->where('locale', app()->getLocale())
                        ->select('id','category_id','name');
                })
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
            'name_en' => 'required|string|max:255|unique:category_translations,name',
            'name_ka' => 'required|string|max:255|unique:category_translations,name',
        ]);
        $category = Category::create([
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

    public function update(Request $request, Category $category)
    {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ka' => 'required|string|max:255',
        ]);

        $category->translations()->updateOrInsert(
            [
                'locale' => 'ka',
                'category_id' => $category->id
            ],
            [
                'name' => request('name_ka')
            ]
        );

        $category->translations()->updateOrInsert(
            [
                'locale' => 'en',
                'category_id' => $category->id],
            [
                'name' => request('name_en')
            ]);

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

    public function destroy(Category $category)
    {
        $user = auth()->user();

        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category->translations()->delete();

        return response()->json(['message' => 'Deleted successfully']);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CategoryTranslations;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()->whereHas('translations', function ($query) {
            return $query->where('locale', app()->getLocale());
        })->with(['translations' => function ($query) {

            $query->where('locale', app()->getLocale())->select('id', 'product_id', 'name');
        }])
            ->when($request->input('categoryId'), function ($query) use ($request) {
                return $query->where('category_id', $request->input('categoryId'));
            })
            ->when($request->input('min_price'), function ($query) use ($request) {
                return $query->where('price', '>=',$request->input('min_price'));
            })
            ->when($request->input('max_price'), function ($query) use ($request) {
                return $query->where('price', '<=',$request->input('max_price'));
            })
            ->withcount(['favorites','favorites as user_favorite' => function ($query) {
                return $query->where('favorites.user_id', auth()->id());
            }])
            ->get();

        return response()->json([
            'products' => $products,
        ]);

    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ka' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer'
        ]);

        $product = Product::create([
            'price' => $request->price,
            'category_id' => $request->category_id,
            'user_id' => auth()->id()
        ]);

        $product->translations()->create([
            'name' => $request->name_ka,
            'locale' => 'ka',
        ]);
        $product->translations()->create([
            'name' => $request->name_en,
            'locale' => 'en',
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);

    }
    public function update(Request $request, Product $product)
    {
        $user = auth()->user();

        if ($product->user_id != $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->update([
            'price' => $request->input('price')
        ]);

        $product->translations()->updateOrInsert(
            [
                'locale' => 'ka',
                'product_id' => $product->id,
            ],
            [
                'name' => request('name_ka'),
            ]
        );

        $product->translations()->updateOrInsert(
            [
                'locale' => 'en',
                'product_id' => $product->id,
            ],
            [
                'name' => request('name_en'),
            ]
        );

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);

    }
    public function destroy(Product $product)
    {
        $user = auth()->user();

        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($product->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->translations()->delete();

        return response()->json(['message' => 'Deleted']);

    }

}

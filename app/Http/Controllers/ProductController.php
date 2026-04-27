<?php

namespace App\Http\Controllers;

use App\Models\Favorites;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
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

        return response()->json($products);

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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer'
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $request->category_id,
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);

    }
    public function update(Request $request ,$id)
    {
        $user = auth()->user();

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // 🔒 ownership check
        if ($product->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $product->update($validated);

        return response()->json($product);

    }
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // 🔒 ownership check
        if ($product->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);

    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {

    return response()->json(Product::all());

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

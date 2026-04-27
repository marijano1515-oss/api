<?php

namespace App\Http\Controllers;

use App\Models\Favorites;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(
            $user->favorites()->with('category')->get()
        );
    }

    public function store(Request $request,$product_id)
    {
        $user = User::user();

        // check if product exists
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // prevent duplicate
        $exists = Favorites::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Already in favorites'
            ], 409);
        }

        Favorites::create([
            'user_id' => $user->id,
            'product_id' => $product_id
        ]);

        return response()->json([
            'message' => 'Added to favorites'
        ], 201);
    }
    public function destroy($product_id)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $favorite = Favorites::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Not in favorites'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Removed from favorites'
        ]);
    }
}

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

        $favorites =Favorites::where('user_id',$user->id)->with('products')->get();

        return response()->json($favorites);
    }

    public function store(Request $request,Product $product)
    {
        $user =auth()->user();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        Favorites::FirstOrCreate([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        return response()->json([
            'message' => 'Added to favorites'
        ], 201);
    }
    public function destroy(Product $product,Favorites $favorite)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

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

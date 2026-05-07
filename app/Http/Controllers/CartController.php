<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $items = CartItems::where('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('product')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = CartItems::where('cart_id', $cart->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($item) {
            $item->increment('quantity', $validated['quantity']);
        } else {
            CartItems::create([
                'cart_id' => $cart->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json([
            'message' => 'Added to cart'
        ]);
    }

    public function update(CartItems $item)
    {
        $user = auth()->user();

        if ($item->cart->user_id != $user->id) {
            return response()->json([
                "message" => "unauthorized"
            ]);
        }

        $item->update([
                'quantity' => request('quantity')
            ]);

        return response()->json(['message' => 'Updated']);
    }
    public function destroy( CartItems $item)
    {
        $user = auth()->user();

        if ($item->cart->user_id != $user->id) {

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $item->delete();

        return response()->json(['message' => 'Removed']);
    }

}

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
    if(!$user)
    {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    $cart = Cart::firstOrCreate([
        'user_id'=>$user-id
    ]);
    return response()->json([
        $cart->load('items.product')
    ]);

}
public function store(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

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
public function update(Request $request,$product_id)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $validated = $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $cart = Cart::where('user_id', $user->id)->first();


    $item = CartItems::where('cart_id', $cart->id)
        ->where('product_id', $product_id)
        ->first();

    if (!$item) {
        return response()->json(['message' => 'Item not found'], 404);
    }

    $item->increment(['quantity' => $validated['quantity']]);

    return response()->json(['message' => 'Updated']);
}
public function destroy(Request $request,$product_id)
{
    $user = auth()->user();

    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['message' => 'Cart not found'], 404);
    }
    $item = CartItems::where('cart_id', $cart->id)->where('product_id', $product_id)->first();

    if (!$item)
    {
        return response()->json(['message' => 'Item not found'], 404);
    }
    if($item->quantity > 0)
    {
    $item->decrement();
    }
    return response()->json(['message' => 'Removed']);
}
}

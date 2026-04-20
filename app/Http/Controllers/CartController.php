<?php

namespace App\Http\Controllers;

use App\Models\Cart;
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

}
public function update(Request $request)
{
}
public function destroy(Request $request)
{

}

}

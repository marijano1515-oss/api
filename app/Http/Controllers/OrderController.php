<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request, )
    {
        $user=auth()->user();

        $cart = Cart::with('items.product')
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }
        $total = 0;
        foreach($cart->items as $item)
        {
            $total += $item->product->price * $item->quantity;
        }

        $order = Orders::create(['user_id'=> $user->id,
            'total'=>$total,
            'status'=>'pending' ]);
        foreach($order->items as $item)
        {
            OrderItems::create([
                'order_id'=>$order->id,
                'product_id'=>$item->product_id,
                'quantity'=>$item->quantity,
                'price'=>$item->product->price
            ]);
        }
        if($order->status == 'pending')
        {
            $cart->items()->delete();
        }
        return response()->json(['order created succesfully',
            'order' => $order,
            'items' => $order->items
            ]);
    }

    public function update(Request $request, Orders $order)
    {
        $user=auth()->user();

        if($order->user_id == $user->id){

            return response()->json([
            'message' => 'You cannot edit your own orders'
            ]);

        }
        if($order->status == 'pending')
        {
            $order->update([
                'status'=>'completed'
            ]);
        }

        return response()->json(["message"=>'order updated succesfully',]);


    }

    public function index()
    {
        return response()->json(Orders::with("items.quantity")
            ->orderby('created_at','desc')
            ->get());
    }

    public function show($id)
    {

        return response()->json(Orders::with('products')
            ->findOrFail($id));

    }

    public function destroy(Orders $order)
    {
        $user = auth()->user();
        if(!$order){
            return response()->json(['not found']);
        }
        if($user->id != $order->user_id){
            return response()->json(['not allowed']);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order  has been deleted successfully'
        ], 200);
    }
}

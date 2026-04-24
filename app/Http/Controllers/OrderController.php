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
    public function store(Request $request)
    {
        $user = Auth::user();

        // get user's cart with items + products
        $cart = Cart::with('items.product')
            ->where('user_id', $user->id)
            ->first();

        // check if cart exists or is empty
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
        // insert data into orders table [user_id, total, status]

        $order = Orders::create(['user_id'=> $user->id,
            'total'=>$total,
            'status'=>'pending' ]);
        // get order id and create order_items from cart items
        foreach($order->items as $item)
        {
            OrderItems::create([
                'order_id'=>$order->id,
                'product_id'=>$item->product_id,
                'quantity'=>$item->quantity,
                'price'=>$item->product->price
            ]);
        }
        // delete cart and cart items
        if($order->status == 'pending')
        {
            $cart->items()->delete();
        }
        // return response
        return response()->json(['order created succesfully',
            'order' => $order,
            'items' => $order->items
            ]);
    }


    public function update(Request $request, $id)
{
//   $user = auth()->user();
//
//    $order = Orders::where('user_id' == $user->id,
//    'order_id' == $order->id
//    );
//    if(!$order){
//        return response()->json(['404']);
//    }
//    if('status' == 'pending')
//    {
//        $order->update();
//    }
//    $validated = $request->validate([
//        'status' => 'required|in:pending,received,on road,canceled',
//    ]);


}

    public function index()
    {
        return response()->json(Orders::with("items.quantity")->orderby('created_at','desc')->get());
    }

    public function show($id)
    {
        return response()->json(Orders::with('products')->findOrFail($id));
    }
    public function destroy($id)
    {
        $order = Orders::findOrFail($id);

        if ($order->status != 'pending') {
            return response()->json([
                'error' => 'Cannot delete an order'
            ], 403);
        }
        else $order->delete();

        return response()->json([
            'message' => 'Order  has been deleted successfully'
        ], 200);
    }
}

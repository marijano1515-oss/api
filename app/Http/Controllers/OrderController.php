<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
//    $user = auth()->user();
//        $cartItems = CartItems::where('user_id', $user->id)->with('product')->get();
//        if(!$cart || $cart->$items->empty())
//        {
//        return response()->json(['message' => 'Cart is empty'], 400);
//        }
//
//        DB:beginTransaction();
//        try
//        {
//            $order = Orders::create([
//                'user_id' => $user->id,
//                'total_price' => 0,
//                'status' => 'pending',
//
//            ]);
//            foreach ($cart->items as $item) {
//
//                $price = $item->product->price;
//                $subtotal = $price * $item->quantity;
//
//                $total += $subtotal;
//
//                OrdersItems::create([
//                    'order_id' => $order->id,
//                    'product_id' => $item->product_id,
//                    'quantity' => $item->quantity,
//                    'price' => $price,
//                ]);
//            }
//        }

    }
    public function update(Request $request, $id)
{
    $order = Orders::findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|in:pending,received,on road,canceled',
    ]);

    $order->update([
        'status' => $validated['status']
    ]);

    return response()->json([
        'message' => 'Order status updated successfully',
        'order' => $order
    ], 200);
}

    public function index()
    {
        return response()->json(Orders::with('products')->get());
    }

    public function show($id)
    {
        return response()->json(Orders::with('products')->findOrFail($id));
    }
    public function destroy($id)
    {
        $order = Orders::findOrFail($id);

        if ($order->status === 'on road') {
            return response()->json([
                'error' => 'Cannot delete an order that is already on the road'
            ], 403);
        }


        $order->delete();

        return response()->json([
            'message' => 'Order #'.$id.' has been deleted successfully'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Orders; // Your model name
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the input (The 'items' array)
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // 2. Use a transaction to ensure both tables update correctly
        return DB::transaction(function () use ($validated) {

            // 3. Insert into 'orders' table
            $order = Orders::create([
                'user_id' => auth()->id() ?? 1,
                'status' => 'pending',
                'total_price' => 0,
            ]);

            $total = 0;

            // 4. Loop through items to insert into 'order_items' table
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                // This is where the magic happens:
                // Laravel takes the $order->id and inserts it into order_items automatically
                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price' => $product->price // Snapshot of price
                ]);
            }


        });
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

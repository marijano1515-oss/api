<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
public function index()
{

}
public function store(Request $request)
{
    $validated = $request->validate([
    'items' => 'required|array',
    'items.*.product_id' => 'required|exists:products,id',
    'items.*.quantity' => 'required|integer|min:1',
]);

    return DB::transaction(function () use ($validated) {
        // Create the base order
        $order = Orders::create([
            'user_id' => auth()->id() ?? 1,
            'status' => 'pending',
            'total_price' => 0,
        ]);

        $total = 0;

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            // Save the relationship and the price at this moment
            $order->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);
        }

        $order->update(['total_price' => $total]);

        return response()->json($order->load('products'), 201);
    });
}
public function update(Request $request, $id)
{
    $order = Orders::findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|in:pending,received,on road,canceled',
        'total_price' => 'sometimes|numeric' // Only if you want to manually override
    ]);

    $order->update($validated);

    return response()->json([
        'message' => 'Order updated successfully',
        'order' => $order
    ]);
}
public function destroy($id)
{
    $order = Orders::findOrFail($id);

    // Optional: Prevent deletion if the order is already "on road"
    if ($order->status === 'on road') {
        return response()->json(['error' => 'Cannot delete an order that is already on the road'], 403);
    }

    $order->delete();

    return response()->json([
        'message' => 'Order deleted successfully'
    ], 200);
}
}

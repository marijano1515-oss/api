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

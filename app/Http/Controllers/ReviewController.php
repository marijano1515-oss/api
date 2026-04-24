<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {   $user = auth()->user();
        $product_id = request('product_id');
        $reviews = Review::with('user_id')->where('product_id', $product_id)->get();
        return response()->json([
            'user_id' => $user->id,
                'product_id' => $product_id,
                'description' => $reviews->description,

            ]
        );
    }
    public function store()
    {
        $user = auth()->user();

    $validated = request()->validate([
        'product_id' => 'required|exists:products,id',
        'rating' => 'required|integer|min:1|max:10',
        'description' => 'required|string|max:255',
    ]);
        $review = Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'description' => $validated['description'],

        ]);
        return response()->json(['review posted' => $review]);
    }
    public function update()
    {
        $user = auth()->user();
        $validated = request()->validate([
            'description' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:10'
        ]);

        $review = Review::where(['user_id'=>$user->id])->update(['description' => $validated['description'], 'rating' => $validated['rating']]);
        return response()->json(['review updated' => $review]);
    }
    public function destroy()
    {
        $user = auth()->user();

        $review = Review::where(['user_id' => $user->id])->delete();
        return response()->json(['review deleted']);
    }

}

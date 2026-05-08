<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::all());
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
    public function update(Review $review)
    {
        $user = auth()->user();

        $validated = request()->validate([
            'description' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:10'
        ]);

        $review->where(['user_id'=>$user->id])
            ->update(['description' => $validated['description'], 'rating' => $validated['rating']]);

        return response()->json([
            'message' => 'Review updated',
            'review' => request(["description", "rating"])
        ]);
    }
    public function destroy(Review $review)
    {
        $user = auth()->user();

        $review->where(['id' => $review->id])->where(['user_id'=>$user->id])
            ->delete();

//        $review->where('id', $review->id)
//            ->where('user_id', $user->id)
//            ->delete();

        return response()->json([
            "message"=>'review deleted'
        ]);
    }

}

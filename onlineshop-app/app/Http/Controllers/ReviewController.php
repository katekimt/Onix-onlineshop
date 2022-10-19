<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use App\Http\Requests\ReviewRequest;
use App\Models\User;


class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function store(ReviewRequest $request, Product $product)
    {
        $request->validated();
        $review = new Review;
        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->user_id = auth()->user()->id;
        $product->reviews()->save($review);
        return response()->json(['message' => 'Review Added', 'review' => $review]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @param \App\Review $review
     * @return \Illuminate\Http\Response
     */
    public function update(ReviewRequest $request, Product $product, Review $review)
    {
        if (auth()->user()->id == $product->user_id || auth()->user()->isAdmin()) {
            $review->update($request->validated());
            return response()->json(['message' => 'Review Updated', 'review' => $review]);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     * @param \App\Review $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Review $review)
    {
        if (auth()->user()->id == $product->user_id || auth()->user()->isAdmin()) {
            $review->delete();
            return response()->json(null, 204);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }
}

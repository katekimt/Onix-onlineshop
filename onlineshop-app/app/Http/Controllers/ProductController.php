<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductRequest;
use App\Models\User;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$products = Product::with('user:id,name')
            ->withCount('reviews')
            ->latest()
            ->paginate(20);
        return response()->json(['products' => $products]);*/
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $created_product = Product::create($request->validated());
        return response()->json(['message' => 'Product Added', 'product' => $created_product]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load(['reviews' => function ($query) {
            $query->latest();
        }, 'user']);
        return response()->json(['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        if (auth()->user()->id == $product->user_id || auth()->user()->isAdmin()) {
            $product->update($request->validated());
            return response()->json(['message' => 'Product Updated', 'product' => $product]);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (auth()->user()->id == $product->user_id || auth()->user()->isAdmin()) {
            $product->delete();
            return response()->json(null, 204);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }
}

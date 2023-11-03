<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $created_product = Product::create($request->validated());

        return response()->json(['message' => 'Product Added', 'product' => $created_product]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['reviews' => function ($query) {
            $query->latest();
        }, 'user']);

        return response()->json(['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        if (auth()->user()->id == $product->user_id || auth()->user()->isAdmin()) {
            $product->update($request->validated());

            return response()->json(['message' => 'Product Updated', 'product' => $product]);
        }

        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        if (auth()->user()->id == $product->user_id || auth()->user()->isAdmin()) {
            $product->delete();

            return response()->json(null, 204);
        }

        return response()->json(['message' => 'Action Forbidden']);
    }
}

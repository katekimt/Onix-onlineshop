<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Http\Resources\CartResource;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return CartResource::collection(Cart::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CartRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'integer|required|required',
            'quantity' => 'required|numeric',
            'product_id' => 'integer|required|required',
        ]);

        $create_cart = Cart::create([
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
        ]);

        $cart_id = $create_cart->id;

        DB::table('cart_product')->insert([
            'product_id' => $request->product_id,
            'cart_id' => $cart_id,
        ]);

        foreach ($create_cart->products as $product) {
            Product::find($product->id)->decrement('in_stick', $create_cart->quantity);
        }
        return new CartResource($create_cart);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new CartResource(Cart::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CartRequest $request
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function update(CartRequest $request, Cart $cart)
    {
        $cart->update($request->validated());
        return new CartResource($cart);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

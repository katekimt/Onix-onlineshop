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
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

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
            'quantity' => 'required|numeric',
            'product_id' => 'integer|required',
        ]);

        $create_cart = Cart::create([
            'user_id' => auth()->user()->id,
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
    public function show(Cart $cart)
    {
        if (auth()->user()->id === $cart->user_id) {
            return new CartResource(Cart::findOrFail($cart->id));
        }
        return response()->json(['message' => 'Action Forbidden']);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CartRequest $request
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        if (auth()->user()->id === $cart->user_id) {
            $old_quantity = $cart->quantity;
            $old_product_id = $cart->products->keyBy('id')->keys();

            $request->validate([
                'quantity' => 'required|numeric',
                'product_id' => 'integer|required|required',
            ]);

            $cart->update([
                'quantity' => $request->quantity,
            ]);

            DB::table('cart_product')
                ->where('cart_id', $cart->id)
                ->update(['product_id' => $request->product_id]);

            foreach ($cart->products as $product) {
                Product::find($old_product_id[0])->increment('in_stick', $old_quantity);
                Product::find($product->id)->decrement('in_stick', $cart->quantity);
            }
            return new CartResource($cart);
        }

        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        if (auth()->user()->id === $cart->user_id) {
            DB::table('cart_product')->where('cart_id', "=", $cart->id)->delete();
            $cart->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        }
        return response()->json(['message' => 'Action Forbidden']);
    }
}

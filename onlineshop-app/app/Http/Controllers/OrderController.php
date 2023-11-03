<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
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
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return OrderResource
     */
    public function store(OrderRequest $request)
    {
        $request->validated();

        $create_order = Order::create([
            'user_id' => auth()->user()->id,
            'status' => $request->status,
            'comment' => $request->comment,
            'address' => $request->address,
        ]);
        DB::table('product_order')->insert([
            'product_id' => $request->product_id,
            'order_id' => $create_order->id,
        ]);

        return new OrderResource($create_order);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        if (auth()->user()->id === $order->user_id || auth()->user()->isAdmin()) {
            return new OrderResource(Order::findOrFail($order->id));
        }

        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderRequest $request, Order $order)
    {
        if (auth()->user()->id === $order->user_id) {
            $request->validated();
            $order->update([
                'status' => $request->status,
                'comment' => $request->comment,
                'address' => $request->address,
            ]);
            DB::table('product_order')
                ->where('order_id', $order->id)
                ->update([
                    'product_id' => $request->product_id,
                ]);

            return new OrderResource($order);
        }

        return response()->json(['message' => 'Action Forbidden']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if (auth()->user()->id === $order->user_id || auth()->user()->isAdmin()) {
            DB::table('product_order')->where('order_id', '=', $order->id)->delete();
            DB::table('order_items')->where('order_id', '=', $order->id)->delete();
            $order->delete();

            return response(null, Response::HTTP_NO_CONTENT);
        }

        return response()->json(['message' => 'Action Forbidden']);
    }

    public function getOrderItem()
    {
        $orderId = DB::table('orders')->where('user_id', auth()->user()->id)->pluck('id');
        $products_id = DB::table('product_order')->where('order_id', $orderId[0])->pluck('product_id');
        $quantity = 0;
        $price = 0;
        for (; $quantity < count($products_id); $quantity++) {
            $price += DB::table('products')
                ->where('id', $products_id[$quantity])
                ->sum('price');
        }

        return OrderItem::create([
            'order_id' => $orderId[0],
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }
}

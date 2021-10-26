<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;

class OrdersController extends Controller
{
    public function __construct() {
        return $this->middleware('auth:api');
    }

    public function placeOrder(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => ['required', 'integer']
        ]);

        if($validator->passes()) {
            $order = new Order($request->all());
            $order->save();

            $product_id = Order::with('Product')->latest()->first()->product_id;

            $product = Product::with('Order')->findOrFail($product_id);

            // getting the difference of the availability of the product
            $result = $product->available_stock - $request['quantity'];

            if($result <= 0) {
                return response()->json(['status' => 'error', 'message' => trans('responses.order.failed.invalid_stocks')], 400);
            }

            $product->available_stock = $result;

            $product->save();

            return response()->json(['status' => 'success', 'message' => trans('responses.order.success.complete')], 201);
        }

        return response()->json(['status' => 'error', 'message' => $validator->errors()->all()], 422);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Place an order
    public function placeOrder(Request $request)
    {
        // Validate request inputs
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'delivery_address' => 'required|string',
            'delivery_option' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create the order
        $order = Order::create([
            'user_id' => auth()->id(),  // Get the authenticated user's ID
            'items' => $request->items,
            'delivery_address' => $request->delivery_address,
            'delivery_option' => $request->delivery_option,
            'payment_status' => 'pending',
            'total_amount' => $request->total_amount,
            'order_status' => 'pending',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Order placed successfully', 'order' => $order]);
    }

    // Get all orders of the authenticated user
    public function getOrders()
    {
        $orders = Order::where('user_id', auth()->id())->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }

    // Get the status of a specific order
    public function getOrderStatus($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or does not belong to the authenticated user'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status
        ]);
    }

    // Update the order status (Admin or User)
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order || $order->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or does not belong to the authenticated user',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'order_status' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Order updated successfully', 'order' => $order]);
    }
} 
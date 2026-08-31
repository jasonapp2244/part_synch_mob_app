<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EarningController extends Controller
{
    public function EarningRecords()
    {
        $orders = Order::with(['orderItems', 'orderItems.product'])->latest()->get();

        $totalOrders = $orders->count();
        $totalRevenue = OrderItem::sum('total_price');
        $pendingOrders = $orders->where('order_status', 'pending')->count();
        $completedOrders = $orders->whereIn('order_status', ['delivered', 'payment', 'completed'])->count();

        return view('admin.view_earning_records', compact(
            'orders', 'totalOrders', 'totalRevenue', 'pendingOrders', 'completedOrders'
        ));
    }

    public function orderDetails($id)
    {
        $order = Order::with([
            'orderItems.product.productImages',
            'deliveryAddress'
        ])->findOrFail($id);

        $customer = User::find($order->user_id);
        $vendor = User::find($order->vendor_id);

        return view('admin.view_order_details', compact('order', 'customer', 'vendor'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => ['required', Rule::in(['pending', 'accept', 'payment', 'order_packed', 'shipping', 'completed', 'delivered', 'cancel'])],
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;

        if ($request->order_status === 'delivered') {
            $order->delivery_date = now();
        }

        $order->save();

        return redirect()->route('order.details', $id)->with('success', 'Order status updated successfully.');
    }
}

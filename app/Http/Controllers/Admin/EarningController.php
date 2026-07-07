<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function EarningRecords()
    {
        $orders = Order::with(['orderItems', 'orderItems.product'])->latest()->get();

        $totalOrders = $orders->count();
        $totalRevenue = OrderItem::sum('total_price');
        $pendingOrders = $orders->where('order_status', 'pending')->count();
        $completedOrders = $orders->whereIn('order_status', ['delivered', 'payment'])->count();

        return view('admin.view_earning_records', compact(
            'orders', 'totalOrders', 'totalRevenue', 'pendingOrders', 'completedOrders'
        ));
    }
}

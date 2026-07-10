<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalOrders   = Order::count();
        $totalRevenue  = (float) OrderItem::sum('total_price');

        $totalVendors  = User::where('role_id', 2)->count();
        $activeVendors = User::where('role_id', 2)->where('status', 'active')->count();

        $totalCustomers  = User::where('role_id', 3)->count();
        $activeCustomers = User::where('role_id', 3)->where('status', 'active')->count();

        $recentOrders = Order::with(['orderItems.product'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalVendors',
            'activeVendors',
            'totalCustomers',
            'activeCustomers',
            'recentOrders'
        ));
    }

    public function users()
    {
        return view('admin.users');
    }
}

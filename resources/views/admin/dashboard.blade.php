@extends('layouts.admin')
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Orders</p>
                                    <h4 class="my-1 text-info">{{ number_format($totalOrders) }}</h4>
                                    <p class="mb-0 font-13">{{ $totalProducts }} products listed</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-cart'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Revenue</p>
                                    <h4 class="my-1 text-danger">${{ number_format($totalRevenue, 2) }}</h4>
                                    <p class="mb-0 font-13">From all orders</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-wallet'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Vendors</p>
                                    <h4 class="my-1 text-success">{{ $totalVendors }}</h4>
                                    <p class="mb-0 font-13">Active {{ $activeVendors }}</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-bar-chart-alt-2'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Customers</p>
                                    <h4 class="my-1 text-warning">{{ $totalCustomers }}</h4>
                                    <p class="mb-0 font-13">Active {{ $activeCustomers }}</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bxs-group'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->

            <div class="card radius-10">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Recent Orders</h6>
                        </div>
                        <div class="font-13 ms-auto">
                            <span class="badge bg-gradient-quepal">{{ $totalOrders }} total</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Order Number</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $order->order_number ?? 'N/A' }}</strong></td>
                                    <td>{{ $order->orderItems->count() }} item(s)</td>
                                    <td>${{ number_format($order->orderItems->sum('total_price'), 2) }}</td>
                                    <td>
                                        @if($order->order_status === 'delivered')
                                            <span class="badge bg-gradient-quepal text-white shadow-sm w-100">Delivered</span>
                                        @elseif($order->order_status === 'pending')
                                            <span class="badge bg-gradient-blooker text-white shadow-sm w-100">Pending</span>
                                        @elseif($order->order_status === 'payment')
                                            <span class="badge bg-gradient-blues text-white shadow-sm w-100">Payment</span>
                                        @elseif($order->order_status === 'cancelled')
                                            <span class="badge bg-gradient-bloody text-white shadow-sm w-100">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary text-white shadow-sm w-100">{{ ucfirst($order->order_status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recent orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection

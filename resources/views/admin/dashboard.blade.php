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
                                    <p class="mb-0 font-13">All time orders</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-cart'></i>
                                </div>
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
                                    <p class="mb-0 font-13">Gross order value</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-wallet'></i>
                                </div>
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
                                    <h4 class="my-1 text-success">{{ number_format($totalVendors) }}</h4>
                                    <p class="mb-0 font-13">Active {{ number_format($activeVendors) }}</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-bar-chart-alt-2'></i>
                                </div>
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
                                    <h4 class="my-1 text-warning">{{ number_format($totalCustomers) }}</h4>
                                    <p class="mb-0 font-13">Active {{ number_format($activeCustomers) }}</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bxs-group'></i>
                                </div>
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Photo</th>
                                    <th>Order #</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    @php
                                        $firstItem = $order->orderItems->first();
                                        $product = $firstItem?->product;
                                        $amount = $order->orderItems->sum('total_price');
                                        $status = strtolower($order->order_status ?? 'pending');
                                        $statusMap = [
                                            'delivered' => ['label' => 'Delivered', 'class' => 'bg-gradient-quepal', 'width' => 100],
                                            'payment'   => ['label' => 'Paid', 'class' => 'bg-gradient-quepal', 'width' => 100],
                                            'shipped'   => ['label' => 'Shipped', 'class' => 'bg-gradient-blooker', 'width' => 80],
                                            'pending'   => ['label' => 'Pending', 'class' => 'bg-gradient-blooker', 'width' => 50],
                                            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-gradient-bloody', 'width' => 30],
                                        ];
                                        $badge = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gradient-blooker', 'width' => 50];
                                    @endphp
                                    <tr>
                                        <td>{{ $product->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($product && $product->product_pic)
                                                <img src="{{ asset('storage/' . $product->product_pic) }}" class="product-img-2" alt="product img">
                                            @else
                                                <img src="{{ asset('assets/images/products/01.png') }}" class="product-img-2" alt="product img">
                                            @endif
                                        </td>
                                        <td>#{{ $order->order_number ?? $order->id }}</td>
                                        <td><span class="badge {{ $badge['class'] }} text-white shadow-sm w-100">{{ $badge['label'] }}</span></td>
                                        <td>${{ number_format($amount, 2) }}</td>
                                        <td>{{ optional($order->created_at)->format('d M Y') }}</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $badge['class'] }}" role="progressbar" style="width: {{ $badge['width'] }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No orders found.</td>
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

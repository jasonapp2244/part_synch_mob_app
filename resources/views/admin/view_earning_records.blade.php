@extends('layouts.admin')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tables</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Earning Table</li>
                        </ol>
                    </nav>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <h6 class="mb-0 text-uppercase">Earning Overview</h6>
            <hr />

            <!-- Summary Cards -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mb-3">
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Orders</p>
                                    <h4 class="my-1 text-info">{{ $totalOrders }}</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-cart'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total Revenue</p>
                                    <h4 class="my-1 text-success">${{ number_format($totalRevenue, 2) }}</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bx-dollar'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Pending Orders</p>
                                    <h4 class="my-1 text-warning">{{ $pendingOrders }}</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bx-time'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Completed</p>
                                    <h4 class="my-1 text-danger">{{ $completedOrders }}</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bx-check-circle'></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Order #</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $order->order_number ?? 'N/A' }}</td>
                                    <td>{{ $order->orderItems->count() }}</td>
                                    <td>${{ number_format($order->orderItems->sum('total_price'), 2) }}</td>
                                    <td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'delivered' => 'bg-success',
                                                'completed' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'payment' => 'bg-info',
                                                'accept' => 'bg-primary',
                                                'order_packed' => 'bg-info',
                                                'shipping' => 'bg-primary',
                                                'cancel' => 'bg-danger',
                                                'cancelled' => 'bg-danger',
                                            ];
                                            $color = $statusColors[$order->order_status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $color }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                                    </td>
                                    <td>{{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('order.details', $order->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bx bx-show"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No order records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

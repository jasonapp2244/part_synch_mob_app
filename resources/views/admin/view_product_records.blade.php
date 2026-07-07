@extends('layouts.admin')
@section('title', 'Products')

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Catalog</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Products</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-gradient-orange text-white rounded-pill px-3 py-2 shadow-sm">{{ $products->count() }} Products</span>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card radius-10 overflow-hidden">
                <div class="card-header bg-gradient-orange p-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0 text-white"><i class="bx bx-box me-2"></i>Product Records</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="fw-bold">{{ $product->name ?? 'N/A' }}</span></td>
                                    <td><span class="fw-bold text-primary">${{ number_format($product->price ?? 0, 2) }}</span></td>
                                    <td>
                                        @if(($product->stock_quantity ?? 0) <= 0)
                                            <span class="badge bg-gradient-bloody text-white rounded-pill px-3 shadow-sm">Out of Stock</span>
                                        @elseif(($product->stock_quantity ?? 0) < 10)
                                            <span class="badge bg-gradient-blooker text-white rounded-pill px-3 shadow-sm">{{ $product->stock_quantity }} Left</span>
                                        @else
                                            <span class="badge bg-gradient-lush text-white rounded-pill px-3 shadow-sm">{{ $product->stock_quantity }} In Stock</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->user ? $product->user->first_name . ' ' . $product->user->last_name : 'N/A' }}</td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-gradient-quepal text-white rounded-pill px-3 shadow-sm">Active</span>
                                        @else
                                            <span class="badge bg-gradient-bloody text-white rounded-pill px-3 shadow-sm">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-inverse-primary">
                                            <i class="bx bx-show me-0"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No product records found.</td>
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

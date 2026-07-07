@extends('layouts.admin')
@section('title', 'Vendors')

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Vendors</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Vendor Records</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-gradient-quepal text-white rounded-pill px-3 py-2 shadow-sm">{{ $vendors->count() }} Total Vendors</span>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card radius-10 overflow-hidden">
                <div class="card-header bg-gradient-deepblue p-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0 text-white"><i class="bx bx-store-alt me-2"></i>Vendor Records</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Name</th>
                                    <th>Business Type</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendors as $index => $vendor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="widgets-icons rounded-circle bg-gradient-scooter text-white me-2" style="width:32px;height:32px;font-size:13px;">
                                                {{ strtoupper(substr($vendor->first_name ?? 'V', 0, 1)) }}
                                            </div>
                                            {{ $vendor->first_name }} {{ $vendor->last_name }}
                                        </div>
                                    </td>
                                    <td>{{ $vendor->business_type ?? 'N/A' }}</td>
                                    <td>{{ $vendor->phone_number ?? 'N/A' }}</td>
                                    <td>
                                        @if($vendor->status === 'active')
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
                                    <td colspan="6" class="text-center text-muted py-4">No vendor records found.</td>
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

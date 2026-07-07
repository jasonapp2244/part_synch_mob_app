@extends('layouts.admin')
@section('title', 'Companies')

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
                            <li class="breadcrumb-item active" aria-current="page">Companies</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-gradient-ibiza text-white rounded-pill px-3 py-2">{{ $companies->count() }} Total</span>
                </div>
            </div>
            <!--end breadcrumb-->

            <h6 class="mb-0 text-uppercase">Company Records</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Company Name</th>
                                    <th>Vendor</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companies as $index => $company)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $company->company_name ?? 'N/A' }}</td>
                                    <td>{{ $company->user ? $company->user->first_name . ' ' . $company->user->last_name : 'N/A' }}</td>
                                    <td>{{ Str::limit($company->description ?? 'N/A', 50) }}</td>
                                    <td>
                                        @if($company->status === 'active')
                                            <span class="badge bg-gradient-quepal text-white rounded-pill px-3">Active</span>
                                        @else
                                            <span class="badge bg-gradient-bloody text-white rounded-pill px-3">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-show me-0"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No company records found.</td>
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

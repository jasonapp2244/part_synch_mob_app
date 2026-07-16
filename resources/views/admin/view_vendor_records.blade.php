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
                            <li class="breadcrumb-item active" aria-current="page">Vendors Table</li>
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

            <h6 class="mb-0 text-uppercase">Vendor Records</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Name</th>
                                    <th>Email</th>
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
                                    <td>{{ $vendor->first_name }} {{ $vendor->last_name }}</td>
                                    <td>{{ $vendor->email ?? 'N/A' }}</td>
                                    <td>{{ $vendor->business_type ?? 'N/A' }}</td>
                                    <td>{{ $vendor->phone_number ?? 'N/A' }}</td>
                                    <td>
                                        <form action="{{ route('vendor.toggle.status', $vendor->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $vendor->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                                {{ ucfirst($vendor->status ?? 'inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewVendorModal{{ $vendor->id }}">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteVendorModal{{ $vendor->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No vendor records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($vendors as $vendor)
    <!-- View Vendor Modal -->
    <div class="modal fade" id="viewVendorModal{{ $vendor->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vendor Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $vendor->first_name }} {{ $vendor->last_name }}</p>
                            <p><strong>Email:</strong> {{ $vendor->email ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $vendor->phone_number ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> <span class="badge {{ $vendor->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($vendor->status ?? 'inactive') }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Business Type:</strong> {{ $vendor->business_type ?? 'N/A' }}</p>
                            <p><strong>Business Description:</strong> {{ $vendor->business_description ?? 'N/A' }}</p>
                            <p><strong>Business License:</strong> {{ $vendor->business_license ?? 'N/A' }}</p>
                            <p><strong>Joined:</strong> {{ $vendor->created_at ? $vendor->created_at->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    @if($vendor->business_logo)
                        <div class="mt-2">
                            <strong>Business Logo:</strong><br>
                            <img src="{{ asset('storage/' . $vendor->business_logo) }}" alt="Logo" width="100" class="rounded mt-1">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteVendorModal{{ $vendor->id }}" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="{{ route('vendor.destroy', $vendor->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Vendor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong>{{ $vendor->first_name }} {{ $vendor->last_name }}</strong>?</p>
                        <div class="alert alert-warning py-2">This action cannot be undone.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection

@extends('layouts.admin')
@section('title', 'Categories')

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
                            <li class="breadcrumb-item active" aria-current="page">Categories</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-gradient-ohhappiness text-white rounded-pill px-3 py-2 shadow-sm">{{ $categories->count() }} Categories</span>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card radius-10 overflow-hidden">
                <div class="card-header bg-gradient-ohhappiness p-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0 text-white"><i class="bx bx-category me-2"></i>Category Records</h6>
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
                                    <th>Description</th>
                                    <th>Sub Categories</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $index => $category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="fw-bold">{{ $category->name ?? 'N/A' }}</span></td>
                                    <td>{{ Str::limit($category->description ?? 'N/A', 50) }}</td>
                                    <td><span class="badge bg-gradient-scooter text-white rounded-pill px-3 shadow-sm">{{ $category->sub_categories_count }}</span></td>
                                    <td>{{ $category->created_at ? $category->created_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-inverse-primary">
                                            <i class="bx bx-show me-0"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No category records found.</td>
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

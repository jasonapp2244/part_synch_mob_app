@extends('layouts.admin')

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tables</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Sub Category Table</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <h6 class="mb-0 text-uppercase">Sub Category Records</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Sub Category Name</th>
                                    <th>Parent Category</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subCategories as $index => $subCategory)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $subCategory->sub_category_name ?? 'N/A' }}</td>
                                    <td>{{ $subCategory->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                {{ $subCategory->status === 'active' ? 'checked' : '' }}
                                                style="background-color: #a1a1a1;" disabled>
                                            <label class="form-check-label">{{ ucfirst($subCategory->status ?? 'inactive') }}</label>
                                        </div>
                                    </td>
                                    <td>{{ $subCategory->created_at ? $subCategory->created_at->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-secondary">
                                            <i class="bx bx-show me-0"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No sub category records found.</td>
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

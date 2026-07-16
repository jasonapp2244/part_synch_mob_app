@extends('layouts.admin')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Settings</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Settings</li>
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

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                <!-- General Settings -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bx bx-cog"></i> General Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" value="{{ $currentSettings['site_name'] }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Admin Email</label>
                                <input type="email" class="form-control" name="site_email" value="{{ $currentSettings['site_email'] }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="site_phone" value="{{ $currentSettings['site_phone'] }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Currency Code</label>
                                <input type="text" class="form-control" name="currency" value="{{ $currentSettings['currency'] }}" placeholder="USD">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Currency Symbol</label>
                                <input type="text" class="form-control" name="currency_symbol" value="{{ $currentSettings['currency_symbol'] }}" placeholder="$">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Settings -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bx bx-money"></i> Commission Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Commission Rate</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="vendor_commission_rate" value="{{ $currentSettings['vendor_commission_rate'] }}" step="0.01" min="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Commission Type</label>
                                <select class="form-select" name="commission_type">
                                    <option value="percentage" {{ $currentSettings['commission_type'] === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ $currentSettings['commission_type'] === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Settings -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bx bx-cart"></i> Order Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Minimum Order Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $currentSettings['currency_symbol'] }}</span>
                                    <input type="number" class="form-control" name="min_order_amount" value="{{ $currentSettings['min_order_amount'] }}" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Default Tax Rate</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="tax_rate" value="{{ $currentSettings['tax_rate'] }}" step="0.01" min="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Default Shipping Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $currentSettings['currency_symbol'] }}</span>
                                    <input type="number" class="form-control" name="shipping_default" value="{{ $currentSettings['shipping_default'] }}" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

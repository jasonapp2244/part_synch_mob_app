<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('admin/images/favicon-32x32.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Part Synch</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        <li>
            <a href="{{ route('vendor.records') }}" class="{{ request()->routeIs('vendor.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-store-alt'></i></div>
                <div class="menu-title">Vendors</div>
            </a>
        </li>

        <li>
            <a href="{{ route('user.records') }}" class="{{ request()->routeIs('user.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-group'></i></div>
                <div class="menu-title">Users</div>
            </a>
        </li>

        <li>
            <a href="{{ route('featured.records') }}" class="{{ request()->routeIs('featured.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-star'></i></div>
                <div class="menu-title">Featured</div>
            </a>
        </li>

        <li class="menu-label">Catalog</li>

        <li>
            <a href="{{ route('category.records') }}" class="{{ request()->routeIs('category.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-category'></i></div>
                <div class="menu-title">Categories</div>
            </a>
        </li>

        <li>
            <a href="{{ route('sub.category.records') }}" class="{{ request()->routeIs('sub.category.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-subdirectory-right'></i></div>
                <div class="menu-title">Sub Categories</div>
            </a>
        </li>

        <li>
            <a href="{{ route('company.records') }}" class="{{ request()->routeIs('company.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-buildings'></i></div>
                <div class="menu-title">Companies</div>
            </a>
        </li>

        <li>
            <a href="{{ route('product.records') }}" class="{{ request()->routeIs('product.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-box'></i></div>
                <div class="menu-title">Products</div>
            </a>
        </li>

        <li class="menu-label">Finance</li>

        <li>
            <a href="{{ route('earning.records') }}" class="{{ request()->routeIs('earning.records') ? 'mm-active' : '' }}">
                <div class="parent-icon"><i class='bx bx-wallet'></i></div>
                <div class="menu-title">Earnings</div>
            </a>
        </li>

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->

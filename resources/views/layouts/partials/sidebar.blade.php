<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            {{-- <img src="assets/images/logo-icon.png" class="logo-icon" alt="logo icon"> --}}
        </div>
        <div>
            <h4 class="logo-text">Part Synch</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
   <ul class="metismenu" id="menu">

    <li>
        <a href="{{ route('admin.dashboard') }}">
            <div class="parent-icon"><i class='bx bx-grid-alt'></i></div>
            <div class="menu-title">Dashboard</div>
        </a>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-store-alt'></i></div>
            <div class="menu-title">Vendors</div>
        </a>
        <ul>
            <li><a href="{{ route('vendor.records') }}"><i class='bx bx-radio-circle'></i>Manage Vendors</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-user'></i></div>
            <div class="menu-title">Users</div>
        </a>
        <ul>
            <li><a href="{{ route('user.records') }}"><i class='bx bx-radio-circle'></i>Manage Users</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-category'></i></div>
            <div class="menu-title">Category</div>
        </a>
        <ul>
            <li><a href="{{ route('category.records') }}"><i class='bx bx-radio-circle'></i>Manage Categories</a></li>
            <li><a href="{{ route('sub.category.records') }}"><i class='bx bx-radio-circle'></i>Manage Sub Categories</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-buildings'></i></div>
            <div class="menu-title">Company</div>
        </a>
        <ul>
            <li><a href="{{ route('company.records') }}"><i class='bx bx-radio-circle'></i>Manage Companies</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-box'></i></div>
            <div class="menu-title">Products</div>
        </a>
        <ul>
            <li><a href="{{ route('product.records') }}"><i class='bx bx-radio-circle'></i>Manage Products</a></li>
            <li><a href="{{ route('featured.records') }}"><i class='bx bx-radio-circle'></i>Featured Products</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-wallet'></i></div>
            <div class="menu-title">Orders & Earnings</div>
        </a>
        <ul>
            <li><a href="{{ route('earning.records') }}"><i class='bx bx-radio-circle'></i>Manage Orders</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class='bx bx-rocket'></i></div>
            <div class="menu-title">Boost</div>
        </a>
        <ul>
            <li><a href="{{ route('boost.packages') }}"><i class='bx bx-radio-circle'></i>Manage Packages</a></li>
        </ul>
    </li>

    <li>
        <a href="{{ route('admin.settings') }}">
            <div class="parent-icon"><i class='bx bx-cog'></i></div>
            <div class="menu-title">Settings</div>
        </a>
    </li>

</ul>

    <!--end navigation-->
</div>
<!--end sidebar wrapper -->

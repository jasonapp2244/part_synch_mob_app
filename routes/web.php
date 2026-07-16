<?php
use App\Http\Controllers\Admin\{
    AuthController,
    DashboardController,
    VendorController,
    UserController,
    FeaturedController,
    CategoryController,
    SubCategoryController,
    CompanyController,
    ProductController,
    EarningController,
    SettingsController,
    BoostPackageController
};

use Illuminate\Support\Facades\Route;


Route::get('/test', function () {
    return "Route is working!";
});

/*
|--------------------------------------------------------------------------
| Admin Authentication (guest)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Panel (protected: authenticated admins only)
|--------------------------------------------------------------------------
*/
Route::middleware('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

    // Vendor Management
    Route::get('/vendor-records', [VendorController::class, 'VendorRecords'])->name('vendor.records');
    Route::post('/vendor/{id}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendor.toggle.status');
    Route::delete('/vendor/{id}', [VendorController::class, 'destroy'])->name('vendor.destroy');

    // User Management
    Route::get('/user-records', [UserController::class, 'UserRecords'])->name('user.records');
    Route::post('/user/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle.status');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Featured
    Route::get('/featured-records', [FeaturedController::class, 'FeaturedRecords'])->name('featured.records');
    Route::post('/featured/{id}/toggle', [FeaturedController::class, 'toggleFeatured'])->name('featured.toggle');

    // Category CRUD
    Route::get('/category-records', [CategoryController::class, 'CategoryRecords'])->name('category.records');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

    // Sub Category CRUD
    Route::get('/sub-category-records', [SubCategoryController::class, 'SubCategoryRecords'])->name('sub.category.records');
    Route::post('/sub-category', [SubCategoryController::class, 'store'])->name('sub.category.store');
    Route::put('/sub-category/{id}', [SubCategoryController::class, 'update'])->name('sub.category.update');
    Route::post('/sub-category/{id}/toggle-status', [SubCategoryController::class, 'toggleStatus'])->name('sub.category.toggle.status');
    Route::delete('/sub-category/{id}', [SubCategoryController::class, 'destroy'])->name('sub.category.destroy');

    // Company CRUD
    Route::get('/company-records', [CompanyController::class, 'CompanyRecords'])->name('company.records');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::put('/company/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::post('/company/{id}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('company.toggle.status');
    Route::delete('/company/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');

    // Product Management
    Route::get('/product-records', [ProductController::class, 'ProductRecords'])->name('product.records');
    Route::post('/product/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('product.toggle.status');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

    // Order Management
    Route::get('/earning-records', [EarningController::class, 'EarningRecords'])->name('earning.records');
    Route::get('/order/{id}', [EarningController::class, 'orderDetails'])->name('order.details');
    Route::post('/order/{id}/update-status', [EarningController::class, 'updateOrderStatus'])->name('order.update.status');

    // Boost Package Management
    Route::get('/boost-packages', [BoostPackageController::class, 'list'])->name('boost.packages');
    Route::post('/boost-package', [BoostPackageController::class, 'store'])->name('boost.package.store');
    Route::put('/boost-package/{id}', [BoostPackageController::class, 'update'])->name('boost.package.update');
    Route::post('/boost-package/{id}/toggle-status', [BoostPackageController::class, 'toggleStatus'])->name('boost.package.toggle.status');
    Route::delete('/boost-package/{id}', [BoostPackageController::class, 'destroy'])->name('boost.package.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');

});

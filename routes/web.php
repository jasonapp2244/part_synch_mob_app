<?php
use App\Http\Controllers\Admin\{
    DashboardController,
    VendorController,
    UserController,
    FeaturedController,
    CategoryController,
    SubCategoryController,
    CompanyController,
    ProductController,
    EarningController

};

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/test', function () {
    return "Route is working!";
});

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');


Route::get('/vendor-records', [VendorController::class, 'VendorRecords'])->name('vendor.records');


Route::get('/user-records', [UserController::class, 'UserRecords'])->name('user.records');


Route::get('a/featured-records', [FeaturedController::class, 'FeaturedRecords'])->name('featured.records');


Route::get('/category-records', [CategoryController::class, 'CategoryRecords'])->name('category.records');


Route::get('sub-category-records', [SubCategoryController::class, 'SubCategoryRecords'])->name('sub.category.records');


Route::get('/company-records', [CompanyController::class, 'CompanyRecords'])->name('company.records');


Route::get('/product-records', [ProductController::class, 'ProductRecords'])->name('product.records');


Route::get('/earning-records', [EarningController::class, 'EarningRecords'])->name('earning.records');


Route::post('/logout', function () {
    if (Auth::check()) {
        Auth::logout();
    }
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/dashboard');
})->name('admin.logout');

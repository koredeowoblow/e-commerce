<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

Route::get('', function () {
    //return view('');
});
// Dashboard route
Route::middleware('auth:api-admin')->get('dashboard/info', [AdminDashController::class, 'index'])->name('dashboard-info');
Route::get('dashboard', [AdminDashController::class, 'view'])->name('dashboard');


// Product routes
Route::prefix('product')->name('product.')->group(function () {
    Route::get('/', [AdminProductController::class, 'index'])->name('index');
    Route::get('checkout', [AdminProductController::class, 'checkout'])->name('checkout');
    Route::post('create', [AdminProductController::class, 'create'])->name('create');
    Route::get('find/{id}', [AdminProductController::class, 'find'])->name('find');
    Route::get('fetch', [AdminProductController::class, 'fetchProducts'])->name('fetch');
    Route::put('update/{id}', [AdminProductController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [AdminProductController::class, 'destroy'])->name('delete');
    // Route::get('categories/{categoryId}', [AdminProductController::class, 'getAttributesByCategory'])->name('attributes');
    Route::get('category', [AdminProductController::class, 'getCategory'])->name('category');
    Route::get('getSubCategoriesByCategory/{id}', [AdminProductController::class, 'getSubCategoriesByCategory'])->name('getSubCategoriesByCategory');
    Route::get('getChildCategoriesBySubCategory/{id}', [AdminProductController::class, 'getChildCategoriesBySubCategory'])->name('getChildCategoriesBySubCategory');
    Route::get('getBrandsByCategory/{id}', [AdminProductController::class, 'getBrandsByCategory'])->name('getBrandsByCategory');
    Route::get('getBrandsBySubCategory/{id}', [AdminProductController::class, 'getBrandsBySubCategory'])->name('getBrandsBySubCategory');
    Route::get('getBrandsByChildCategory/{id}', [AdminProductController::class, 'getBrandsByChildCategory'])->name('getBrandsByChildCategory');
});

// Category routes
Route::prefix('category')->name('category.')->group(function () {
    Route::get('fetch', [AdminCategoryController::class, 'findALLCategory'])->name('fetch');
    Route::post('create', [AdminCategoryController::class, 'createCategory'])->name('create');
    Route::get('find/{id}', [AdminCategoryController::class, 'findCategory'])->name('find');
    Route::put('update/{id}', [AdminCategoryController::class, 'updateCategory'])->name('update');
    Route::delete('delete/{id}', [AdminCategoryController::class, 'deleteCategory'])->name('delete');
});

// Subcategory routes
Route::prefix('subcategory')->name('subCategory.')->group(function () {
    Route::get('fetch', [AdminCategoryController::class, 'findALLSubcategory'])->name('fetch');
    Route::post('create', [AdminCategoryController::class, 'createSubcategory'])->name('create');
    Route::get('find/{id}', [AdminCategoryController::class, 'findSubcategory'])->name('find');
    Route::put('update/{id}', [AdminCategoryController::class, 'updateSubcategory'])->name('update');
    Route::delete('delete/{id}', [AdminCategoryController::class, 'deleteSubcategory'])->name('delete');
});

// Childcategory routes
Route::prefix('childcategory')->name('childcategory')->group(function () {
    Route::get('fetch', [AdminCategoryController::class, 'findALLChildcategory'])->name('fetch');
    Route::post('create', [AdminCategoryController::class, 'createChildCategory'])->name('create');
    Route::get('find/{id}', [AdminCategoryController::class, 'findChildcategory'])->name('find');
    Route::put('update/{id}', [AdminCategoryController::class, 'updateChildcategory'])->name('update');
    Route::delete('delete/{id}', [AdminCategoryController::class, 'deleteChildcategory'])->name('delete');
});

// Brand routes
Route::prefix('brand')->name('brand.')->group(function () {
    Route::get('fetch', [AdminCategoryController::class, 'findALLBrand'])->name('fetch');
    Route::post('create', [AdminCategoryController::class, 'createBrand'])->name('create');
    Route::get('find/{id}', [AdminCategoryController::class, 'findBrand'])->name('find');
    Route::put('update/{id}', [AdminCategoryController::class, 'updateBrand'])->name('update');
    Route::delete('delete/{id}', [AdminCategoryController::class, 'deleteBrand'])->name('delete');
});

// Vendor routes
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('fetch', [AdminVendorController::class, 'getVendorsWithProductCount'])->name('fetch');
    Route::post('create', [AdminVendorController::class, 'create'])->name('create');
    Route::get('find/{id}', [AdminVendorController::class, 'find'])->name('find');
    Route::put('update/{id}', [AdminVendorController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [AdminVendorController::class, 'delete'])->name('delete');
});

// User routes
Route::prefix('user')->name('user.')->group(function () {
    Route::get('fetch', [AdminUserController::class, 'fetchALL'])->name('fetch');
    Route::post('create', [AdminUserController::class, 'create'])->name('create');
    Route::get('find/{id}', [AdminUserController::class, 'find'])->name('find');
    Route::put('update/{id}', [AdminUserController::class, 'update'])->name('update');
});

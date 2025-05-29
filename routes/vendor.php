<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Vendor\DashboardController as VendorDashController;
use App\Http\Controllers\Api\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Api\Vendor\ProfileController as VendorProfileController;
use App\Http\Controllers\Api\Vendor\VendorChatController;



Route::get('dashboard', [VendorDashController::class, 'index'])->middleware('auth:api-vendor')->name('dashboard');

Route::prefix('product')->middleware('auth:api-vendor')->name('product.')->group(function () {
    Route::get('fetch', [VendorProductController::class, 'FetchAllproduct'])->name('fetch');
    Route::get('find/{id}', [VendorProductController::class, 'find'])->name('find');
    Route::post('create', [VendorProductController::class, 'createProduct'])->name('create');
    Route::put('update/{id}', [VendorProductController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [VendorProductController::class, 'delete'])->name('delete');
    Route::get('category', [VendorProductController::class, 'getCategory'])->name('getcategory');
    Route::get('getSubCategoriesByCategory/{id}', [VendorProductController::class, 'getSubCategoriesByCategory'])->name('subcategories');
    Route::get('getChildCategoriesBySubCategory/{id}', [VendorProductController::class, 'getChildCategoriesBySubCategory'])->name('getChildCategoriesBySubCategory');
    Route::get('getBrandsByCategory/{id}', [VendorProductController::class, 'getBrandsByCategory'])->name('getBrandsByCategory');
    Route::get('getBrandsBySubCategory/{id}', [VendorProductController::class, 'getBrandsBySubCategory'])->name('getBrandsBySubCategory');
    Route::get('getBrandsByChildCategory/{id}', [VendorProductController::class, 'getBrandsByChildCategory'])->name('getBrandsByChildCategory');
});

Route::prefix('profile')->middleware('auth:api-vendor')->name('profile')->group(function () {
    Route::post('update/{id}', [VendorProfileController::class, 'update'])->name('update');
    Route::get('fetch', [VendorProfileController::class, 'index'])->name('fetch');
});

Route::middleware('auth:vendor')->group(function () {
    Route::get('/vendor/chat/{userId}', [VendorChatController::class, 'index']);
    Route::post('/vendor/chat/send/{userId}', [VendorChatController::class, 'send']);
    Route::get('/vendor/chat/unread/{userId}', [VendorChatController::class, 'unreadFromUser']);
});

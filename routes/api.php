<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\SearchController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\UserChatController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;


Route::get("/", function () {
    return response()->json(["message" => "Welcome to the E-commerce API"]);
});
Route::prefix('products')->group(function () {
    Route::get('/home', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

Route::get('/search/{title}', [SearchController::class, 'search']);

Route::get('/categories', [SearchController::class, 'getCategory']);
Route::get('/sub-categories/{categoryId}', [SearchController::class, 'getSubCategoriesByCategory']);
Route::get('/child-categories/{subCategoryId}', [SearchController::class, 'getChildCategoriesBySubCategory']);

Route::get('/brand', [SearchController::class, 'getBrands']);

Route::middleware('auth:api-user')->get('/profile', [ProfileController::class, 'index']);
// User side
// User
Route::middleware('auth:api-user')->group(function () {
    Route::get('/user/chat/{vendorId}', [UserChatController::class, 'index']);
    Route::post('/user/chat/send/{vendorId}', [UserChatController::class, 'send']);
    Route::get('/user/chat/unread/{vendorId}', [UserChatController::class, 'unreadFromVendor']);
});
;

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'index')->name('user.register');
    Route::post('/register', 'registerUser')->name('user.store');

    Route::get('/vendor/register', 'index')->name('vendor.register');
    Route::post('/vendor/register', 'registerVendor')->name('vendor.store');

    Route::get('/admin/register', 'index')->name('admin.register');
    Route::post('/register/admin', 'registerAdmin')->name('admin.store');

    Route::get('/fetch-country', 'fetch_country')->name('fetch.country');
    Route::get('/fetch-state/{id}', 'fetch_state')->name('fetch.state');

    Route::post('/verify-email', 'verifyEmail')->name('email.verify');
});
Route::post('login', [LoginController::class, 'store']);

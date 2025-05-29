<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; // You need this import

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::prefix('vendor')->middleware('auth:api-vendor')->name('vendor.')->group(function () {
            require base_path('routes/vendor.php');
        });

        Route::prefix('api') ->name('api.')->group(function () {
            require base_path('routes/api.php');
        });

        Route::prefix('admin')->middleware('auth:api-admin')->name('admin.')->group(function () {
            require base_path('routes/admin.php');
        });

        $this->loadRoutesFrom(base_path('routes/web.php'));
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceListController;
use App\Http\Controllers\CustomerDetailController;
use App\Http\Controllers\ProductInfoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return [
            'user' => $request->user(),
            'currentToken' => $request->bearerToken(),
        ];
    })->name('user.info');

    Route::post('user/logout', [UserController::class, 'logout'])->name('user.logout');

    // Service List routes
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceListController::class, 'index'])->name('services.index');
        Route::get('{id}', [ServiceListController::class, 'show'])->whereNumber('id')->name('services.show');
        Route::post('/', [ServiceListController::class, 'store'])->name('services.store');
        Route::put('{id}', [ServiceListController::class, 'update'])->whereNumber('id')->name('services.update');
        Route::delete('{id}', [ServiceListController::class, 'destroy'])->whereNumber('id')->name('services.destroy');
    });

    // Customer Detail routes
    Route::prefix('customer-details')->group(function () {
        Route::get('/', [CustomerDetailController::class, 'index'])->name('customer-details.index');
        Route::get('{id}', [CustomerDetailController::class, 'show'])->whereNumber('id')->name('customer-details.show');
        Route::post('/', [CustomerDetailController::class, 'store'])->name('customer-details.store');
        Route::put('{id}', [CustomerDetailController::class, 'update'])->whereNumber('id')->name('customer-details.update');
        Route::delete('{id}', [CustomerDetailController::class, 'destroy'])->whereNumber('id')->name('customer-details.destroy');
    });

    // Product Info routes
    Route::prefix('product-infos')->group(function () {
        Route::get('/', [ProductInfoController::class, 'index'])->name('product-infos.index');
        Route::get('{id}', [ProductInfoController::class, 'show'])->whereNumber('id')->name('product-infos.show');
        Route::post('/', [ProductInfoController::class, 'store'])->name('product-infos.store');
        Route::put('{id}', [ProductInfoController::class, 'update'])->whereNumber('id')->name('product-infos.update');
        Route::delete('{id}', [ProductInfoController::class, 'destroy'])->whereNumber('id')->name('product-infos.destroy');
    });
});

// User registration and login routes
Route::post('user/register', [UserController::class, 'store'])->name('user.register');
Route::post('user/login', [UserController::class, 'auth'])->name('user.login');

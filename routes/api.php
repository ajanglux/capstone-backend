<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceListController;
use App\Http\Controllers\CustomerDetailController;
use App\Http\Controllers\ProductInfoController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\VerifyEmailController;
use App\Models\User;

// Emails
Route::post('user/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('user/reset-password', [UserController::class, 'resetPassword']);

Route::post('/verify-email-code', [VerifyEmailController::class, 'verifyCode'])->name('verification.verifyCode');


Route::get('/email/verify', function (Request $request) {
    return response()->json(['message' => 'Please verify your email']);
})->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
->middleware(['signed'])
->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $email = $request->input('email'); // Get the email from request
    $user = \App\Models\User::where('email', $email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified']);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification email resent']);
})->name('verification.resend');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return [
            'user' => $request->user(),
            'currentToken' => $request->bearerToken(),
        ];
    })->name('user.info');

    Route::post('user/logout', [UserController::class, 'logout'])->name('user.logout');

    // UserController
    Route::get('/user/profile', [UserController::class, 'fetchUserData']);
    Route::put('/user/profile', [UserController::class, 'updateUserProfile']);
    Route::put('/user/change-email', [UserController::class, 'updateEmail']);
    Route::put('/user/change-password', [UserController::class, 'changePassword']);

    // Service List
    Route::prefix('services')->group(function () {
        Route::post('/', [ServiceListController::class, 'store'])->name('services.store');
        Route::put('{id}', [ServiceListController::class, 'update'])->whereNumber('id')->name('services.update');
        Route::delete('{id}', [ServiceListController::class, 'destroy'])->whereNumber('id')->name('services.destroy');
    });

    // Product Info
    Route::prefix('product-infos')->group(function () {
        Route::get('/', [ProductInfoController::class, 'index'])->name('product-infos.index');
        Route::get('{id}', [ProductInfoController::class, 'show'])->whereNumber('id')->name('product-infos.show');
        Route::post('/', [ProductInfoController::class, 'store'])->name('product-infos.store');
        Route::put('{id}', [ProductInfoController::class, 'update'])->whereNumber('id')->name('product-infos.update');
        Route::delete('{id}', [ProductInfoController::class, 'destroy'])->whereNumber('id')->name('product-infos.destroy');
    });

    // Customer Detail
    Route::prefix('customer-details')->group(function () {
        Route::get('/', [CustomerDetailController::class, 'index'])->name('customer-details.index');
        Route::get('{id}', [CustomerDetailController::class, 'show'])->whereNumber('id')->name('customer-details.show');
        Route::post('/', [CustomerDetailController::class, 'store'])->name('customer-details.store');
        Route::put('{id}', [CustomerDetailController::class, 'update'])->name('customer-details.update');
        Route::delete('{id}', [CustomerDetailController::class, 'destroy'])->name('customer-details.destroy');
        Route::patch('{id}/status', [CustomerDetailController::class, 'updateStatus'])->name('customer-details.update-status');
        Route::get('/{id}/with-product-info', [CustomerDetailController::class, 'showWithProductInfo'])->name('customer-details.show-with-product-infos');
        Route::get('/home/status', [CustomerDetailController::class, 'showHomeStatus']);
        Route::put('/comment/{id}', [CustomerDetailController::class, 'comment']);
        Route::get('/check-inquiries', [CustomerDetailController::class, 'checkInquiries']);
        Route::get('/show-description', [CustomerDetailController::class, 'showAllDescriptions']);
        Route::get('/my-list/repair', [CustomerDetailController::class, 'myListRepair']);
        
    });

    // Admin Dashboard
    Route::get('admin-dashboard-stats', [AdminDashboardController::class, 'getDashboardStats'])->name('admin-dashboard.stats');
    Route::get('admin-dashboard-stats/completed-repairs', [AdminDashboardController::class, 'getMonthlyCompletedRepairs'])->name('admin-dashboard.completed-repairs');
});

// Publicly access for services
Route::get('services', [ServiceListController::class, 'index'])->name('services.index');
Route::get('services/{id}', [ServiceListController::class, 'show'])->whereNumber('id')->name('services.show');

// Public Customer Detail
Route::post('customer-details', [CustomerDetailController::class, 'store'])->name('customer-details.store');
Route::get('customer-details/status/{code}', [CustomerDetailController::class, 'showStatus'])->name('customer-details.show-status');

// User registration and login
Route::post('user/register', [UserController::class, 'store'])->name('user.register');
Route::post('user/login', [UserController::class, 'auth'])->name('user.login');

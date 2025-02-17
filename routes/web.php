<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/success', function () {
    return view('email.verify_success');
})->name('email.verify.success');

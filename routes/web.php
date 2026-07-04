<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/login/student', function () {
    return view('auth.login-student');
})->name('login.student');

Route::get('/login/staff', function () {
    return view('auth.login-staff');
})->name('login.staff');

Route::view('/replacement-arrangement', 'replacement-arrangement');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

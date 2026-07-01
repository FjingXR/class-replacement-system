<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');
Route::get('/', function () {
    return view('welcome');
});

Route::view('/login-dummy', 'login-dummy');
Route::view('/replacement-arrangement', 'replacement-arrangement');
Route::view('/replacement-arrangement', 'replacement-arrangement');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

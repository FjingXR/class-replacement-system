<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/login/student', function () {
    return view('auth.login-student');
})->name('login.student');

Route::get('/login/staff', function () {
    return view('auth.login-staff');
})->name('login.staff');

Route::get('/replacement-arrangement', function () {
    return view('ui-design-templates.replacement-arrangement-UIdesign-template');
});
Route::get('/my-timetable-ui', function () {
    return view('ui-design-templates.MyTimetable-UI-design-template');
});
Route::get('/replacement-home-ui', function () {
    return view('ui-design-templates.replacement-home-UI-design-template');
});
Route::get('/my-request-history-ui', function () {
    return view('ui-design-templates.my-request-history-UI-design-template');
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

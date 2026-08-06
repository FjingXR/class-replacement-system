<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/login/student', function () {
    return view('auth.login-student');
})->name('login.student');

Route::get('/login/staff', function () {
    return view('auth.login-staff');
})->name('login.staff');

Route::get('/replacement-home-ui', function () {
    return view('ui-design-templates.replacement-home-UI-design-template', ['activeNav' => 'replacement-arrangement']);
});

Route::get('/my-timetable-ui', function () {
    return view('ui-design-templates.MyTimetable-UI-design-template', ['activeNav' => 'my-timetable']);
});

Route::get('/my-request-history-ui', function () {
    return view('ui-design-templates.my-request-history-UI-design-template', ['activeNav' => 'replacement-history']);
});

Route::get('/replacement-arrangement', function () {
    return view('ui-design-templates.replacement-arrangement-UIdesign-template', ['activeNav' => 'replacement-arrangement']);
});

Route::get('/cohort-timetable-ui', function () {
    return view('ui-design-templates.CohortTimetable-UI-design-template', ['activeNav' => 'cohort-timetables']);
});

Route::get('/student-my-timetable-ui', function () {
    return view('ui-design-templates.student-my-timetable-UI-design-template', ['activeNav' => 'my-timetable']);
});

Route::get('/request-approval-ui', function () {
    return view('ui-design-templates.request-approval-UI-design-template', ['activeNav' => 'request-approval']);
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

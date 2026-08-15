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
    return view('ui-design-templates.my-request-history-UI-design-template', [
        'activeNav' => 'replacement-history',
        'navItems' => [
            ['key' => 'my-timetable', 'label' => 'My Timetable', 'href' => '/my-timetable-ui'],
            ['key' => 'cohort-timetables', 'label' => 'Cohort Timetables', 'href' => '/cohort-timetable-ui'],
            ['key' => 'replacement-arrangement', 'label' => 'Replacement Arrangement', 'href' => '/replacement-home-ui'],
            ['key' => 'request-approval', 'label' => 'Request Approval', 'href' => '/request-approval-ui', 'badge' => true],
            ['key' => 'replacement-history', 'label' => 'Request History', 'href' => '/my-request-history-ui'],
            ['key' => 'venue-timetable', 'label' => 'Venue Timetable', 'href' => '/venue-timetable-ui'],
        ],
    ]);
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

Route::get('/venue-timetable-ui', function () {
    return view('ui-design-templates.venue-timetable-UI-design-template', ['activeNav' => 'venue-timetable']);
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

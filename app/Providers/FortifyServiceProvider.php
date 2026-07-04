<?php

namespace App\Providers;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::loginView(function () {
            return redirect('/login/student');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'login_type' => ['required', 'string', 'in:student,staff'],
                'login_id' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $loginType = $request->input('login_type');
            $loginId = $request->input('login_id');
            $password = $request->input('password');

            $user = null;

            if ($loginType === 'student') {
                $student = Student::where('student_id', $loginId)->first();
                if ($student) {
                    $user = $student->user;
                }
            } else {
                $lecturer = Lecturer::where('staff_id', $loginId)->first();
                if ($lecturer) {
                    $user = $lecturer->user;
                }
            }

            if ($user && Hash::check($password, $user->password)) {
                return $user;
            }

            return null;
        });

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input('login_id').'|'.$request->input('login_type').'|'.$request->ip())
            );

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\Cohort;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\Programme;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private User $lecturerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $focs = Faculty::create(['faculty_code' => 'FOCS', 'faculty_name' => 'FOCS']);
        $dept = Department::create(['dept_code' => 'DCIT', 'dept_name' => 'DCIT', 'faculty_id' => $focs->id]);
        $prog = Programme::create(['programme_code' => 'RSD', 'programme_name' => 'RSD', 'faculty_id' => $focs->id]);
        $cohort = Cohort::create([
            'programme_id' => $prog->id,
            'current_year' => 1,
            'semester' => 1,
            'tutorial_group' => 1,
            'academic_year' => '2025/26',
            'intake' => 'June 2025',
        ]);

        $this->studentUser = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
        ]);
        Student::create([
            'user_id' => $this->studentUser->id,
            'student_id' => '25RSD0001',
            'cohort_id' => $cohort->id,
        ]);

        $this->lecturerUser = User::factory()->create([
            'role' => 'lecturer',
            'password' => Hash::make('password'),
        ]);
        Lecturer::create([
            'user_id' => $this->lecturerUser->id,
            'staff_id' => '9999',
            'dept_id' => $dept->id,
            'is_pl' => false,
        ]);
    }

    public function test_student_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login.student'));
        $response->assertOk();
    }

    public function test_staff_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login.staff'));
        $response->assertOk();
    }

    public function test_students_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'login_type' => 'student',
            'login_id' => '25RSD0001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->studentUser);
    }

    public function test_lecturers_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'login_type' => 'staff',
            'login_id' => '9999',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->lecturerUser);
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login_type' => 'student',
            'login_id' => '25RSD0001',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_users_cannot_authenticate_with_invalid_login_id(): void
    {
        $response = $this->post('/login', [
            'login_type' => 'student',
            'login_id' => 'NONEXISTENT',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $response = $this->actingAs($this->studentUser)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}

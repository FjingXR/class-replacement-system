<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckPl;
use App\Http\Middleware\CheckRole;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private Faculty $faculty;
    private Department $dept;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/_test-role', function () {
            return 'ok';
        })->middleware('role:student');

        Route::get('/_test-pl', function () {
            return 'ok';
        })->middleware('pl');

        $this->faculty = Faculty::create(['faculty_code' => 'FOCS', 'faculty_name' => 'FOCS']);
        $this->dept = Department::create(['dept_code' => 'DCIT', 'dept_name' => 'DCIT', 'faculty_id' => $this->faculty->id]);
    }

    public function test_check_role_allows_correct_role(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($user)->get('/_test-role');
        $response->assertOk();
    }

    public function test_check_role_blocks_wrong_role(): void
    {
        $user = User::factory()->create(['role' => 'lecturer']);
        $response = $this->actingAs($user)->get('/_test-role');
        $response->assertRedirect('/');
    }

    public function test_check_role_blocks_guest(): void
    {
        $response = $this->get('/_test-role');
        $response->assertRedirect('/');
    }

    public function test_check_pl_allows_pl_lecturer(): void
    {
        $user = User::factory()->create(['role' => 'lecturer']);
        Lecturer::create([
            'user_id' => $user->id,
            'staff_id' => 'PL001',
            'dept_id' => $this->dept->id,
            'is_pl' => true,
        ]);

        $response = $this->actingAs($user)->get('/_test-pl');
        $response->assertOk();
    }

    public function test_check_pl_blocks_non_pl_lecturer(): void
    {
        $user = User::factory()->create(['role' => 'lecturer']);
        Lecturer::create([
            'user_id' => $user->id,
            'staff_id' => 'NPL001',
            'dept_id' => $this->dept->id,
            'is_pl' => false,
        ]);

        $response = $this->actingAs($user)->get('/_test-pl');
        $response->assertRedirect('/');
    }

    public function test_check_pl_blocks_student(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($user)->get('/_test-pl');
        $response->assertRedirect('/');
    }

    public function test_check_pl_blocks_guest(): void
    {
        $response = $this->get('/_test-pl');
        $response->assertRedirect('/');
    }
}

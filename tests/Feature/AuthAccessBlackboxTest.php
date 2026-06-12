<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesZafProctorData;
use Tests\TestCase;

class AuthAccessBlackboxTest extends TestCase
{
    use CreatesZafProctorData;
    use RefreshDatabase;

    /** @test */
    public function public_auth_and_guide_routes_are_reachable_to_guests(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertRedirect(route('login'));
        $this->get(route('register.student.form'))->assertRedirect(route('login'));
        $this->get(route('register.teacher.form'))->assertRedirect(route('login'));
        $this->get(route('password.request'))->assertRedirect(route('login'));
        $this->get(route('guide.download'))
            ->assertOk()
            ->assertHeader('Content-Disposition');
    }

    /** @test */
    public function guest_and_wrong_role_access_are_blocked_by_middleware(): void
    {
        $student = $this->student();

        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('teacher.exams.index'))
            ->assertForbidden();
    }

    /** @test */
    public function login_logout_and_account_status_checks_match_user_state(): void
    {
        $teacher = $this->teacher([
            'email' => 'teacher-login@zafproctor.test',
        ]);
        $inactive = $this->student([
            'email' => 'inactive-login@zafproctor.test',
            'is_active' => false,
        ]);
        $pending = $this->teacher([
            'email' => 'pending-login@zafproctor.test',
            'is_approved' => false,
        ]);

        $this->post(route('login'), [
            'email' => $teacher->email,
            'password' => $this->strongPassword,
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($teacher);
        $this->assertNotNull($teacher->fresh()->last_login_at);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();

        $this->post(route('login'), [
            'email' => $teacher->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->post(route('login'), [
            'email' => $inactive->email,
            'password' => $this->strongPassword,
        ])->assertSessionHasErrors('email');

        $this->post(route('login'), [
            'email' => $pending->email,
            'password' => $this->strongPassword,
        ])->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_creates_auto_approved_students_and_pending_teachers(): void
    {
        $this->seedRoles();
        $class = $this->schoolClass();

        $this->post(route('register.student'), [
            'name' => 'Student Registrant',
            'email' => 'student-registrant@zafproctor.test',
            'student_id' => 'S-100',
            'class_id' => $class->id,
            'phone' => '08123456789',
            'password' => $this->strongPassword,
            'password_confirmation' => $this->strongPassword,
        ])->assertRedirect(route('dashboard', absolute: false));

        $student = User::where('email', 'student-registrant@zafproctor.test')->firstOrFail();
        $this->assertTrue($student->isStudent());
        $this->assertTrue($student->is_active);
        $this->assertTrue($student->is_approved);
        $this->assertAuthenticatedAs($student);

        $this->post(route('logout'));

        $this->post(route('register.teacher'), [
            'name' => 'Teacher Registrant',
            'email' => 'teacher-registrant@zafproctor.test',
            'phone' => '08123456788',
            'password' => $this->strongPassword,
            'password_confirmation' => $this->strongPassword,
        ])->assertRedirect(route('login', absolute: false));

        $teacher = User::where('email', 'teacher-registrant@zafproctor.test')->firstOrFail();
        $this->assertTrue($teacher->isTeacher());
        $this->assertTrue($teacher->is_active);
        $this->assertFalse($teacher->is_approved);
        $this->assertGuest();
    }

    /** @test */
    public function all_role_dashboards_render_for_authenticated_active_users(): void
    {
        $admin = $this->admin();
        $teacher = $this->teacher();
        $student = $this->student();

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($teacher)->get(route('dashboard'))->assertOk();
        $this->actingAs($student)->get(route('dashboard'))->assertOk();
    }
}

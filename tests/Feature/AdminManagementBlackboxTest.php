<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesZafProctorData;
use Tests\TestCase;

class AdminManagementBlackboxTest extends TestCase
{
    use CreatesZafProctorData;
    use RefreshDatabase;

    /** @test */
    public function admin_can_manage_users_and_approval_lifecycle(): void
    {
        $admin = $this->admin();
        $teacherRole = $this->role(Role::TEACHER);
        $adminRole = $this->role(Role::ADMIN);
        $pendingTeacher = $this->teacher(['is_approved' => false]);
        $rejectTeacher = $this->teacher(['is_approved' => false]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'teacher']))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Managed Teacher',
                'email' => 'managed-teacher@zafproctor.test',
                'phone' => '08111111111',
                'role_id' => $teacherRole->id,
                'password' => $this->strongPassword,
                'password_confirmation' => $this->strongPassword,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index', absolute: false));

        $managed = User::where('email', 'managed-teacher@zafproctor.test')->firstOrFail();
        $this->assertTrue($managed->isTeacher());
        $this->assertTrue($managed->is_approved);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Blocked Admin',
                'email' => 'blocked-admin@zafproctor.test',
                'role_id' => $adminRole->id,
                'password' => $this->strongPassword,
                'password_confirmation' => $this->strongPassword,
            ])
            ->assertSessionHasErrors('role_id');

        $this->actingAs($admin)
            ->get(route('admin.users.show', $managed))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $managed))
            ->assertOk();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $managed), [
                'name' => 'Managed Teacher Updated',
                'email' => 'managed-teacher-updated@zafproctor.test',
                'phone' => '08222222222',
                'role_id' => $teacherRole->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index', absolute: false));

        $this->assertSame('Managed Teacher Updated', $managed->fresh()->name);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-status', $managed))
            ->assertRedirect();
        $this->assertFalse($managed->fresh()->is_active);

        $this->actingAs($admin)
            ->get(route('admin.users.pending'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $pendingTeacher))
            ->assertRedirect();
        $this->assertTrue($pendingTeacher->fresh()->is_approved);

        $this->actingAs($admin)
            ->post(route('admin.users.reject', $rejectTeacher))
            ->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $rejectTeacher->id]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $managed))
            ->assertRedirect(route('admin.users.index', absolute: false));
        $this->assertDatabaseMissing('users', ['id' => $managed->id]);
    }

    /** @test */
    public function admin_can_manage_classes_and_student_assignment(): void
    {
        $admin = $this->admin();
        $teacher = $this->teacher();
        $student = $this->student(['class_id' => null]);

        $this->actingAs($admin)->get(route('admin.classes.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.classes.create'))->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.classes.store'), [
                'name' => '1A',
                'grade_level' => '1',
                'description' => 'Class created in blackbox test',
                'homeroom_teacher_id' => $teacher->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.classes.index', absolute: false));

        $class = SchoolClass::where('name', '1A')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.classes.show', $class))->assertOk();
        $this->actingAs($admin)->get(route('admin.classes.edit', $class))->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.classes.add-students', $class), [
                'student_ids' => [$student->id],
            ])
            ->assertRedirect();
        $this->assertSame($class->id, $student->fresh()->class_id);

        $this->actingAs($admin)
            ->delete(route('admin.classes.destroy', $class))
            ->assertRedirect();
        $this->assertDatabaseHas('classes', ['id' => $class->id]);

        $this->actingAs($admin)
            ->delete(route('admin.classes.remove-student', [$class, $student]))
            ->assertRedirect();
        $this->assertNull($student->fresh()->class_id);

        $this->actingAs($admin)
            ->patch(route('admin.classes.update', $class), [
                'name' => '1B',
                'grade_level' => '1',
                'description' => 'Updated class',
                'homeroom_teacher_id' => $teacher->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.classes.index', absolute: false));

        $this->actingAs($admin)
            ->delete(route('admin.classes.destroy', $class->fresh()))
            ->assertRedirect(route('admin.classes.index', absolute: false));
        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }

    /** @test */
    public function admin_can_manage_courses_and_enrollments(): void
    {
        $admin = $this->admin();
        $teacher = $this->teacher();
        $student = $this->student();
        $extraStudent = $this->student();

        $this->actingAs($admin)->get(route('admin.courses.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.courses.create'))->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.courses.store'), [
                'code' => 'BIO101',
                'name' => 'Biology',
                'description' => 'Biology course',
                'teacher_id' => $teacher->id,
                'students' => [$student->id],
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.courses.index', absolute: false));

        $course = Course::where('code', 'BIO101')->firstOrFail();
        $this->assertTrue($course->students()->whereKey($student->id)->exists());

        $this->actingAs($admin)->get(route('admin.courses.show', $course))->assertOk();
        $this->actingAs($admin)->get(route('admin.courses.edit', $course))->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.courses.add-students', $course), [
                'student_ids' => [$extraStudent->id],
            ])
            ->assertRedirect();
        $this->assertTrue($course->students()->whereKey($extraStudent->id)->exists());

        $this->actingAs($admin)
            ->delete(route('admin.courses.remove-student', [$course, $student]))
            ->assertRedirect();
        $this->assertFalse($course->students()->whereKey($student->id)->exists());

        $this->actingAs($admin)
            ->patch(route('admin.courses.update', $course), [
                'code' => 'BIO102',
                'name' => 'Biology Updated',
                'description' => 'Updated biology course',
                'teacher_id' => $teacher->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.courses.index', absolute: false));
        $this->assertSame('BIO102', $course->fresh()->code);

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course->fresh()))
            ->assertRedirect(route('admin.courses.index', absolute: false));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}

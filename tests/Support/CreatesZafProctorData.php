<?php

namespace Tests\Support;

use App\Models\Answer;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSetting;
use App\Models\ProctoringLog;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesZafProctorData
{
    protected string $strongPassword = 'Password1!';

    protected function seedRoles(): array
    {
        return [
            Role::ADMIN => Role::firstOrCreate(
                ['name' => Role::ADMIN],
                ['display_name' => 'Administrator']
            ),
            Role::TEACHER => Role::firstOrCreate(
                ['name' => Role::TEACHER],
                ['display_name' => 'Teacher']
            ),
            Role::STUDENT => Role::firstOrCreate(
                ['name' => Role::STUDENT],
                ['display_name' => 'Student']
            ),
        ];
    }

    protected function role(string $name): Role
    {
        return $this->seedRoles()[$name];
    }

    protected function admin(array $attributes = []): User
    {
        return $this->user(Role::ADMIN, $attributes);
    }

    protected function teacher(array $attributes = []): User
    {
        return $this->user(Role::TEACHER, $attributes);
    }

    protected function student(array $attributes = []): User
    {
        return $this->user(Role::STUDENT, $attributes + [
            'student_id' => 'STD-' . strtoupper(Str::random(8)),
        ]);
    }

    protected function user(string $roleName, array $attributes = []): User
    {
        $defaults = [
            'role_id' => $this->role($roleName)->id,
            'name' => Str::headline($roleName) . ' ' . strtoupper(Str::random(5)),
            'email' => strtolower($roleName) . '-' . Str::uuid() . '@zafproctor.test',
            'password' => Hash::make($this->strongPassword),
            'email_verified_at' => now(),
            'is_active' => true,
            'is_approved' => true,
        ];

        return User::factory()->create(array_merge($defaults, $attributes));
    }

    protected function schoolClass(array $attributes = []): SchoolClass
    {
        return SchoolClass::create(array_merge([
            'name' => 'A',
            'grade_level' => (string) random_int(1, 6),
            'description' => 'Fixture class',
            'is_active' => true,
        ], $attributes));
    }

    protected function course(User $teacher = null, array $attributes = []): Course
    {
        $teacher ??= $this->teacher();

        return Course::create(array_merge([
            'teacher_id' => $teacher->id,
            'code' => 'CRS-' . strtoupper(Str::random(8)),
            'name' => 'Course ' . strtoupper(Str::random(5)),
            'description' => 'Fixture course',
            'is_active' => true,
        ], $attributes));
    }

    protected function enroll(User $student, Course $course): void
    {
        $course->students()->syncWithoutDetaching([
            $student->id => ['enrolled_at' => now()],
        ]);
    }

    protected function exam(User $teacher = null, Course $course = null, array $attributes = [], array $settings = []): Exam
    {
        $teacher ??= $this->teacher();
        $course ??= $this->course($teacher);

        $exam = Exam::create(array_merge([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam ' . strtoupper(Str::random(5)),
            'description' => 'Fixture exam',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
            'access_token' => 'TOK' . strtoupper(Str::random(8)),
        ], $attributes));

        $this->examSettings($exam, $settings);

        return $exam;
    }

    protected function scheduledExam(User $teacher = null, Course $course = null, array $attributes = [], array $settings = []): Exam
    {
        return $this->exam($teacher, $course, array_merge([
            'type' => 'scheduled',
            'start_time' => now()->subMinute(),
            'end_time' => now()->addHour(),
        ], $attributes), $settings);
    }

    protected function examSettings(Exam $exam, array $attributes = []): ExamSetting
    {
        return $exam->settings()->updateOrCreate(
            ['exam_id' => $exam->id],
            array_merge(ExamSetting::getDefaults(), $attributes)
        );
    }

    protected function multipleChoiceQuestion(Exam $exam, array $attributes = [], array $options = []): Question
    {
        $question = Question::create(array_merge([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_MULTIPLE_CHOICE,
            'question' => 'What answer should be selected for this fixture question?',
            'points' => 10,
            'order' => ($exam->questions()->max('order') ?? 0) + 1,
            'explanation' => 'Fixture explanation',
        ], $attributes));

        $options = $options ?: [
            ['option_text' => 'Correct answer', 'is_correct' => true],
            ['option_text' => 'Wrong answer A', 'is_correct' => false],
            ['option_text' => 'Wrong answer B', 'is_correct' => false],
        ];

        foreach (array_values($options) as $index => $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_label' => chr(65 + $index),
                'option_text' => $option['option_text'],
                'is_correct' => $option['is_correct'] ?? false,
                'order' => $index,
            ]);
        }

        return $question->fresh('options');
    }

    protected function essayQuestion(Exam $exam, array $attributes = []): Question
    {
        return Question::create(array_merge([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_ESSAY,
            'question' => 'Explain the fixture concept with enough detail.',
            'points' => 20,
            'order' => ($exam->questions()->max('order') ?? 0) + 1,
            'explanation' => 'Fixture essay explanation',
        ], $attributes));
    }

    protected function attempt(Exam $exam, User $student, array $attributes = []): ExamAttempt
    {
        return ExamAttempt::create(array_merge([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'camera_enabled' => true,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Feature Test Agent',
        ], $attributes));
    }

    protected function answer(ExamAttempt $attempt, Question $question, array $attributes = []): Answer
    {
        return Answer::create(array_merge([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answered_at' => now(),
        ], $attributes));
    }

    protected function proctoringLog(ExamAttempt $attempt, array $attributes = []): ProctoringLog
    {
        return ProctoringLog::create(array_merge([
            'attempt_id' => $attempt->id,
            'user_id' => $attempt->user_id,
            'violation_type' => ProctoringLog::TYPE_TAB_SWITCH,
            'description' => 'Fixture violation',
            'severity' => ProctoringLog::SEVERITY_HIGH,
            'is_reviewed' => false,
        ], $attributes));
    }

    protected function validExamPayload(Course $course, array $overrides = []): array
    {
        return array_merge([
            'course_id' => $course->id,
            'title' => 'HTTP Fixture Exam',
            'description' => 'Created through feature test',
            'type' => 'flexible',
            'duration' => 60,
            'access_token' => 'HTTP' . strtoupper(Str::random(8)),
            'status' => Exam::STATUS_DRAFT,
            'max_attempts' => 1,
            'max_tab_switches' => 5,
            'passing_score' => 60,
            'grade_method' => 'highest',
            'webcam_enabled' => '1',
            'browser_lock_enabled' => '1',
            'tab_switch_detection' => '1',
            'block_keyboard_shortcuts' => '1',
        ], $overrides);
    }

    protected function validQuestionPayload(array $overrides = []): array
    {
        return array_merge([
            'question_type' => Question::TYPE_MULTIPLE_CHOICE,
            'question' => 'Which fixture answer is correct for this test case?',
            'points' => 10,
            'options' => [
                ['text' => 'Correct answer'],
                ['text' => 'Incorrect answer'],
            ],
            'correct_option' => 0,
            'explanation' => 'The first option is configured as correct.',
        ], $overrides);
    }
}

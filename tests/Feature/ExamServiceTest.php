<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSetting;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\User;
use App\Services\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExamServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected ExamService $examService;
    protected User $student;
    protected Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->examService = app(ExamService::class);
        
        // Create roles (use firstOrCreate to avoid duplicates)
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher'], ['display_name' => 'Teacher']);
        
        // Create teacher
        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);
        
        // Create course
        $course = Course::create([
            'name' => 'Test Course',
            'code' => 'TEST101',
            'teacher_id' => $teacher->id,
        ]);
        
        // Create student
        $this->student = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);
        
        // Enroll student
        $course->students()->attach($this->student->id, ['enrolled_at' => now()]);
        
        // Create exam
        $this->exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Test Exam',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        
        // Create exam settings
        ExamSetting::create([
            'exam_id' => $this->exam->id,
            'shuffle_questions' => true,
            'passing_score' => 60,
        ]);
        
        // Create questions
        for ($i = 1; $i <= 5; $i++) {
            $question = Question::create([
                'exam_id' => $this->exam->id,
                'type' => 'multiple_choice',
                'question' => "Question $i",
                'points' => 10,
                'order' => $i,
            ]);
            
            // Create options
            for ($j = 1; $j <= 4; $j++) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_label' => chr(64 + $j),
                    'option_text' => "Option $j",
                    'is_correct' => $j === 1,
                    'order' => $j,
                ]);
            }
        }
    }

    /** @test */
    public function it_can_start_an_exam_attempt()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $this->assertInstanceOf(ExamAttempt::class, $attempt);
        $this->assertEquals(ExamAttempt::STATUS_IN_PROGRESS, $attempt->status);
        $this->assertEquals($this->exam->id, $attempt->exam_id);
        $this->assertEquals($this->student->id, $attempt->user_id);
    }

    /** @test */
    public function it_returns_existing_attempt_if_in_progress()
    {
        $attempt1 = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $attempt2 = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $this->assertEquals($attempt1->id, $attempt2->id);
    }

    /** @test */
    public function it_throws_exception_if_already_submitted()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $this->examService->submitExam($attempt, false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Anda sudah mengerjakan ujian ini.');

        $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );
    }

    /** @test */
    public function it_can_save_and_update_answers()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $question = $this->exam->questions()->first();
        $option = $question->options()->first();

        // Save first answer
        $answer1 = $this->examService->saveAnswer($attempt, $question->id, $option->id);
        $this->assertInstanceOf(Answer::class, $answer1);
        $this->assertEquals($option->id, $answer1->selected_option_id);

        // Update answer
        $option2 = $question->options()->skip(1)->first();
        $answer2 = $this->examService->saveAnswer($attempt, $question->id, $option2->id);
        
        // Should be the same answer (updated)
        $this->assertEquals($answer1->id, $answer2->id);
        $this->assertEquals($option2->id, $answer2->selected_option_id);
    }

    /** @test */
    public function it_throws_exception_when_saving_to_submitted_attempt()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $this->examService->submitExam($attempt, false);
        $attempt->refresh();

        $question = $this->exam->questions()->first();
        $option = $question->options()->first();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ujian sudah selesai. Tidak dapat menyimpan jawaban.');

        $this->examService->saveAnswer($attempt, $question->id, $option->id);
    }

    /** @test */
    public function it_can_submit_exam_and_calculate_score()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        // Answer all questions correctly
        foreach ($this->exam->questions as $question) {
            $correctOption = $question->options()->where('is_correct', true)->first();
            $this->examService->saveAnswer($attempt, $question->id, $correctOption->id);
        }

        $attempt = $this->examService->submitExam($attempt, false);
        $attempt->refresh();

        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertEquals(50, $attempt->score); // 5 questions x 10 points
        $this->assertEquals(100, $attempt->percentage);
        $this->assertTrue($attempt->is_passed);
    }

    /** @test */
    public function it_prevents_double_submit()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        $submitted1 = $this->examService->submitExam($attempt, false);
        $submitted2 = $this->examService->submitExam($submitted1, false);

        // Should return same attempt without error
        $this->assertEquals($submitted1->id, $submitted2->id);
        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $submitted2->status);
    }

    /** @test */
    public function it_shuffles_questions_consistently_per_attempt()
    {
        $attempt = $this->examService->startExam(
            $this->exam,
            $this->student->id,
            '127.0.0.1',
            'Test User Agent'
        );

        // Get questions multiple times
        $questions1 = $this->examService->getQuestionsForAttempt($this->exam, $attempt);
        $questions2 = $this->examService->getQuestionsForAttempt($this->exam, $attempt);

        // Order should be the same for the same attempt
        $order1 = $questions1->pluck('id')->toArray();
        $order2 = $questions2->pluck('id')->toArray();

        $this->assertEquals($order1, $order2);
    }
}

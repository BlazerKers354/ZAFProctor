<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Policies\CoursePolicy;
use App\Policies\ExamAttemptPolicy;
use App\Policies\ExamPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Exam::class, ExamPolicy::class);
        Gate::policy(ExamAttempt::class, ExamAttemptPolicy::class);
    }
}

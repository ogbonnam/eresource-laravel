<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $students = User::query()
            ->where('role', 'student')
            ->count();

        $teachers = User::query()
            ->where('role', 'teacher')
            ->count();

        $courses = Course::query()
            ->where('is_active', true)
            ->count();

        $assignments = Assignment::query()
            ->where('is_published', true)
            ->count();

        $pendingEnrollments = Enrollment::query()
            ->where('status', 'pending')
            ->count();

        $submissions = AssignmentSubmission::query()
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        $awaitingGrading = AssignmentSubmission::query()
            ->where('status', 'submitted')
            ->count();

        $dueSoon = Assignment::query()
            ->where('is_published', true)
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [
                now(),
                now()->addDays(7),
            ])
            ->count();

        return [
            Stat::make('Students', number_format($students))
                ->description('Registered students')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Teachers', number_format($teachers))
                ->description('Registered teachers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Active Courses', number_format($courses))
                ->description('Currently active courses')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Published Assignments', number_format($assignments))
                ->description('Available to students')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),

            Stat::make('Pending Enrolments', number_format($pendingEnrollments))
                ->description('Waiting for approval')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('danger'),

            Stat::make('Submissions', number_format($submissions))
                ->description('Submitted or graded')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success'),

            Stat::make('Awaiting Grading', number_format($awaitingGrading))
                ->description('Need teacher attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Due Within 7 Days', number_format($dueSoon))
                ->description('Published assignments')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('danger'),
        ];
    }
}
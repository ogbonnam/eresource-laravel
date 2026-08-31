<?php

namespace App\Filament\Resources\Courses\Widgets;

use App\Models\Course;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class CourseStatsOverview extends BaseWidget
{
    /*
    |--------------------------------------------------------------------------
    | Current Course
    |--------------------------------------------------------------------------
    */

    public ?Model $record = null;

    /*
    |--------------------------------------------------------------------------
    | Polling
    |--------------------------------------------------------------------------
    |
    | Disable automatic polling for now.
    | We can make this live later if required.
    |
    */

    protected ?string $pollingInterval = null;

    /*
    |--------------------------------------------------------------------------
    | Stats
    |--------------------------------------------------------------------------
    */

    protected function getStats(): array
    {
        /** @var Course $course */
        $course = $this->record;

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $students = $course
            ->enrollments()
            ->where('status', 'active')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Resources
        |--------------------------------------------------------------------------
        */

        $resources = $course
            ->resources()
            ->count();

        $publishedResources = $course
            ->resources()
            ->where('is_published', true)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Assignments
        |--------------------------------------------------------------------------
        */

        $assignments = $course
            ->assignments()
            ->count();

        $publishedAssignments = $course
            ->assignments()
            ->where('is_published', true)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Submissions
        |--------------------------------------------------------------------------
        */

        $submissions = $course
            ->assignments()
            ->withCount('submissions')
            ->get()
            ->sum('submissions_count');

        /*
        |--------------------------------------------------------------------------
        | Graded submissions
        |--------------------------------------------------------------------------
        */

        $gradedSubmissions = $course
            ->assignments()
            ->whereHas('submissions')
            ->with([
                'submissions' => function ($query) {
                    $query->where('status', 'graded');
                },
            ])
            ->get()
            ->sum(
                fn ($assignment) =>
                    $assignment->submissions->count()
            );

        /*
        |--------------------------------------------------------------------------
        | Pending grading
        |--------------------------------------------------------------------------
        */

        $pendingGrading = max(
            0,
            $submissions - $gradedSubmissions
        );

        /*
        |--------------------------------------------------------------------------
        | Submission rate
        |--------------------------------------------------------------------------
        */

        $possibleSubmissions =
            $students * $publishedAssignments;

        $submissionRate = $possibleSubmissions > 0
            ? round(
                ($submissions / $possibleSubmissions) * 100
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Course Status
        |--------------------------------------------------------------------------
        */

        $status = $course->is_active
            ? 'Active'
            : 'Inactive';

        /*
        |--------------------------------------------------------------------------
        | Return stats
        |--------------------------------------------------------------------------
        */

        return [

            Stat::make(
                'Students',
                number_format($students)
            )
                ->description(
                    $students === 1
                        ? '1 active student'
                        : $students . ' active students'
                )
                ->descriptionIcon(
                    'heroicon-m-users'
                )
                ->color('info'),

            Stat::make(
                'Resources',
                number_format($resources)
            )
                ->description(
                    $publishedResources .
                    ' published'
                )
                ->descriptionIcon(
                    'heroicon-m-document-text'
                )
                ->color('success'),

            Stat::make(
                'Assignments',
                number_format($assignments)
            )
                ->description(
                    $publishedAssignments .
                    ' published'
                )
                ->descriptionIcon(
                    'heroicon-m-clipboard-document-list'
                )
                ->color('warning'),

            Stat::make(
                'Submissions',
                number_format($submissions)
            )
                ->description(
                    $pendingGrading > 0
                        ? $pendingGrading . ' awaiting grading'
                        : 'All submissions graded'
                )
                ->descriptionIcon(
                    $pendingGrading > 0
                        ? 'heroicon-m-clock'
                        : 'heroicon-m-check-circle'
                )
                ->color(
                    $pendingGrading > 0
                        ? 'warning'
                        : 'success'
                ),

        ];
    }
}
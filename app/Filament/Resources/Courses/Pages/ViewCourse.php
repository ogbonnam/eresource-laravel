<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.resources.courses.pages.view-course';

    public function getTitle(): string
    {
        return $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Course')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    protected function getViewData(): array
    {
        $course = $this->record;

        /*
        |--------------------------------------------------------------------------
        | Load Course Relationships
        |--------------------------------------------------------------------------
        */

        $course->load([
            'teacher',
            'schoolClass',
            'subject',
            'students',
            'resources',
            'assignments.submissions.student',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $students = $course->students
            ->sortBy('name')
            ->values();

        $activeStudents = $students
            ->filter(
                fn ($student) =>
                    ($student->pivot->status ?? null) === 'active'
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Resources
        |--------------------------------------------------------------------------
        */

        $resources = $course->resources
            ->sortByDesc('updated_at')
            ->values();

        $publishedResources = $resources
            ->where('is_published', true)
            ->values();

        $unpublishedResources = $resources
            ->where('is_published', false)
            ->values();

        $resourceTypes = $resources
            ->groupBy(
                fn ($resource) =>
                    $resource->type ?: 'other'
            )
            ->map(
                fn ($items) =>
                    $items->count()
            );

        /*
        |--------------------------------------------------------------------------
        | Assignments
        |--------------------------------------------------------------------------
        */

        $assignments = $course->assignments
            ->sortByDesc('created_at')
            ->values();

        $publishedAssignments = $assignments
            ->where('is_published', true)
            ->values();

        $draftAssignments = $assignments
            ->where('is_published', false)
            ->values();

        $upcomingAssignments = $assignments
            ->filter(
                fn ($assignment) =>
                    $assignment->due_at &&
                    $assignment->due_at->isFuture()
            )
            ->values();

        $overdueAssignments = $assignments
            ->filter(
                fn ($assignment) =>
                    $assignment->due_at &&
                    $assignment->due_at->isPast()
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Submissions
        |--------------------------------------------------------------------------
        */

        $submissions = $assignments
            ->flatMap(
                fn ($assignment) =>
                    $assignment->submissions
            )
            ->sortByDesc(
                fn ($submission) =>
                    $submission->submitted_at
                    ?? $submission->created_at
            )
            ->values();

        $totalSubmissions = $submissions->count();

        /*
        |--------------------------------------------------------------------------
        | Submitted Submissions
        |--------------------------------------------------------------------------
        */

        $submittedSubmissions = $submissions
            ->filter(
                fn ($submission) =>
                    in_array(
                        $submission->status,
                        ['submitted', 'graded']
                    )
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Graded Submissions
        |--------------------------------------------------------------------------
        */

        $gradedSubmissions = $submissions
            ->filter(
                fn ($submission) =>
                    $submission->status === 'graded'
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Late Submissions
        |--------------------------------------------------------------------------
        */

        $lateSubmissions = $submissions
            ->filter(
                fn ($submission) =>
                    $submission->is_late === true
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Pending Grading
        |--------------------------------------------------------------------------
        */

        $pendingSubmissions = $submissions
            ->filter(
                fn ($submission) =>
                    $submission->status === 'submitted'
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */

        /*
        | Expected submissions:
        |
        | Every active student is expected to submit
        | every published assignment.
        */

        $expectedSubmissions =
            $activeStudents->count()
            *
            $publishedAssignments->count();

        /*
        | Actual submitted work.
        */

        $actualSubmittedSubmissions = $submittedSubmissions
            ->filter(
                fn ($submission) =>
                    $submission->assignment?->is_published === true
            )
            ->count();

        /*
        | Missing submissions.
        */

        $missingSubmissions = max(
            0,
            $expectedSubmissions -
            $actualSubmittedSubmissions
        );

        /*
        | Submission rate.
        */

        $submissionRate = $expectedSubmissions > 0
            ? round(
                (
                    $actualSubmittedSubmissions /
                    $expectedSubmissions
                ) * 100
            )
            : 0;

        /*
        | Grading rate.
        */

        $gradingRate = $actualSubmittedSubmissions > 0
            ? round(
                (
                    $gradedSubmissions->count() /
                    $actualSubmittedSubmissions
                ) * 100
            )
            : 0;

        /*
        | Late submission rate.
        */

        $lateRate = $actualSubmittedSubmissions > 0
            ? round(
                (
                    $lateSubmissions->count() /
                    $actualSubmittedSubmissions
                ) * 100
            )
            : 0;

        /*
        | Pending grading rate.
        */

        $pendingRate = $actualSubmittedSubmissions > 0
            ? round(
                (
                    $pendingSubmissions->count() /
                    $actualSubmittedSubmissions
                ) * 100
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Assignment Analytics
        |--------------------------------------------------------------------------
        */

        $assignmentAnalytics = $publishedAssignments
            ->map(
                function ($assignment) use ($activeStudents) {

                    $assignmentSubmissions =
                        $assignment->submissions;

                    $submitted = $assignmentSubmissions
                        ->filter(
                            fn ($submission) =>
                                in_array(
                                    $submission->status,
                                    ['submitted', 'graded']
                                )
                        );

                    $graded = $assignmentSubmissions
                        ->filter(
                            fn ($submission) =>
                                $submission->status === 'graded'
                        );

                    $late = $assignmentSubmissions
                        ->filter(
                            fn ($submission) =>
                                $submission->is_late === true
                        );

                    $expected = $activeStudents->count();

                    $submissionRate = $expected > 0
                        ? round(
                            (
                                $submitted->count() /
                                $expected
                            ) * 100
                        )
                        : 0;

                    return [
                        'id' => $assignment->id,

                        'title' =>
                            $assignment->title,

                        'submitted' =>
                            $submitted->count(),

                        'graded' =>
                            $graded->count(),

                        'late' =>
                            $late->count(),

                        'missing' =>
                            max(
                                0,
                                $expected -
                                $submitted->count()
                            ),

                        'expected' =>
                            $expected,

                        'submission_rate' =>
                            $submissionRate,

                        'due_at' =>
                            $assignment->due_at,
                    ];
                }
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Student Analytics
        |--------------------------------------------------------------------------
        */

        $studentAnalytics = $activeStudents
            ->map(
                function ($student) use ($publishedAssignments) {

                    $studentSubmissions = collect();

                    foreach ($publishedAssignments as $assignment) {

                        $studentSubmission =
                            $assignment->submissions
                                ->first(
                                    fn ($submission) =>
                                        (int) $submission->student_id ===
                                        (int) $student->id
                                );

                        if ($studentSubmission) {
                            $studentSubmissions->push(
                                $studentSubmission
                            );
                        }
                    }

                    $submitted = $studentSubmissions
                        ->filter(
                            fn ($submission) =>
                                in_array(
                                    $submission->status,
                                    ['submitted', 'graded']
                                )
                        );

                    $graded = $studentSubmissions
                        ->filter(
                            fn ($submission) =>
                                $submission->status === 'graded'
                        );

                    $late = $studentSubmissions
                        ->filter(
                            fn ($submission) =>
                                $submission->is_late === true
                        );

                    $expected =
                        $publishedAssignments->count();

                    $submissionRate = $expected > 0
                        ? round(
                            (
                                $submitted->count() /
                                $expected
                            ) * 100
                        )
                        : 0;

                    $missing = max(
                        0,
                        $expected -
                        $submitted->count()
                    );

                    return [
                        'id' =>
                            $student->id,

                        'name' =>
                            $student->name,

                        'email' =>
                            $student->email,

                        'submitted' =>
                            $submitted->count(),

                        'graded' =>
                            $graded->count(),

                        'late' =>
                            $late->count(),

                        'missing' =>
                            $missing,

                        'expected' =>
                            $expected,

                        'submission_rate' =>
                            $submissionRate,
                    ];
                }
            )
            ->sortByDesc('submission_rate')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Top Students
        |--------------------------------------------------------------------------
        */

        $topStudents = $studentAnalytics
            ->sortByDesc('submission_rate')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Students Needing Attention
        |--------------------------------------------------------------------------
        */

        $studentsNeedingAttention = $studentAnalytics
            ->filter(
                fn ($student) =>
                    $student['missing'] > 0 ||
                    $student['submission_rate'] < 50
            )
            ->sortBy([
                ['missing', 'desc'],
                ['submission_rate', 'asc'],
            ])
            ->take(10)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Assignments Needing Attention
        |--------------------------------------------------------------------------
        */

        $assignmentsNeedingAttention = $assignmentAnalytics
            ->sortByDesc('missing')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Analytics Summary
        |--------------------------------------------------------------------------
        */

        $analyticsSummary = [

            'active_students' =>
                $activeStudents->count(),

            'published_assignments' =>
                $publishedAssignments->count(),

            'expected_submissions' =>
                $expectedSubmissions,

            'actual_submissions' =>
                $actualSubmittedSubmissions,

            'missing_submissions' =>
                $missingSubmissions,

            'submission_rate' =>
                $submissionRate,

            'grading_rate' =>
                $gradingRate,

            'late_rate' =>
                $lateRate,

            'pending_rate' =>
                $pendingRate,
        ];

        /*
        |--------------------------------------------------------------------------
        | Submission Trend
        |--------------------------------------------------------------------------
        |
        | Last 7 days.
        |
        | submitted = submissions made that day
        | graded    = submissions graded/updated that day
        |
        */

        $submissionTrend = collect();

        for ($i = 6; $i >= 0; $i--) {

            $date = now()
                ->subDays($i)
                ->startOfDay();

            $nextDate = $date
                ->copy()
                ->addDay();

            /*
            | Submissions made on this date.
            */

            $submitted = $submissions
                ->filter(
                    function ($submission) use (
                        $date,
                        $nextDate
                    ) {

                        $submittedAt =
                            $submission->submitted_at;

                        return $submittedAt &&
                            $submittedAt >= $date &&
                            $submittedAt < $nextDate;
                    }
                )
                ->count();

            /*
            | Submissions graded on this date.
            |
            | updated_at is used because the submission
            | status changes when it is graded.
            */

            $graded = $submissions
                ->filter(
                    function ($submission) use (
                        $date,
                        $nextDate
                    ) {

                        $updatedAt =
                            $submission->updated_at;

                        return $submission->status === 'graded' &&
                            $updatedAt &&
                            $updatedAt >= $date &&
                            $updatedAt < $nextDate;
                    }
                )
                ->count();

            $submissionTrend->push([
                'date' =>
                    $date->format('M d'),

                'submitted' =>
                    $submitted,

                'graded' =>
                    $graded,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Engagement Data
        |--------------------------------------------------------------------------
        |
        | Used by the engagement chart.
        |
        */

        $engagementData = collect([

            [
                'label' => 'Submitted',
                'value' =>
                    $actualSubmittedSubmissions,
            ],

            [
                'label' => 'Graded',
                'value' =>
                    $gradedSubmissions->count(),
            ],

            [
                'label' => 'Late',
                'value' =>
                    $lateSubmissions->count(),
            ],

            [
                'label' => 'Pending Grading',
                'value' =>
                    $pendingSubmissions->count(),
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Recent Activity
        |--------------------------------------------------------------------------
        */

        $activities = collect();

        /*
        | Course created.
        */

        if ($course->created_at) {

            $activities->push([
                'type' =>
                    'course',

                'title' =>
                    'Course created',

                'description' =>
                    $course->name,

                'date' =>
                    $course->created_at,

                'icon' =>
                    'heroicon-o-academic-cap',
            ]);
        }

        /*
        | Resources.
        */

        foreach ($resources as $resource) {

            $activities->push([
                'type' =>
                    'resource',

                'title' =>
                    'Resource updated',

                'description' =>
                    $resource->title,

                'date' =>
                    $resource->updated_at,

                'icon' =>
                    'heroicon-o-document-text',
            ]);
        }

        /*
        | Assignments.
        */

        foreach ($assignments as $assignment) {

            $activities->push([
                'type' =>
                    'assignment',

                'title' =>
                    'Assignment created',

                'description' =>
                    $assignment->title,

                'date' =>
                    $assignment->created_at,

                'icon' =>
                    'heroicon-o-clipboard-document-list',
            ]);
        }

        /*
        | Submissions.
        */

        foreach ($submissions as $submission) {

            $activities->push([
                'type' =>
                    'submission',

                'title' =>
                    'Student submission',

                'description' =>
                    (
                        $submission->student?->name
                        ?? 'Student'
                    )
                    . ' submitted '
                    .
                    (
                        $submission->assignment?->title
                        ?? 'an assignment'
                    ),

                'date' =>
                    $submission->submitted_at
                    ??
                    $submission->created_at,

                'icon' =>
                    'heroicon-o-paper-airplane',
            ]);
        }

        /*
        | Sort and limit activity.
        */

        $activities = $activities
            ->filter(
                fn ($activity) =>
                    $activity['date']
            )
            ->sortByDesc('date')
            ->take(30)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Return View Data
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | submissionTrend and engagementData are included here.
        |
        */

        return compact(

            /*
            | Course
            */
            'course',

            /*
            | Students
            */
            'students',
            'activeStudents',

            /*
            | Resources
            */
            'resources',
            'publishedResources',
            'unpublishedResources',
            'resourceTypes',

            /*
            | Assignments
            */
            'assignments',
            'publishedAssignments',
            'draftAssignments',
            'upcomingAssignments',
            'overdueAssignments',

            /*
            | Submissions
            */
            'submissions',
            'totalSubmissions',
            'submittedSubmissions',
            'gradedSubmissions',
            'lateSubmissions',
            'pendingSubmissions',

            /*
            | Analytics summary
            */
            'analyticsSummary',

            /*
            | Assignment analytics
            */
            'assignmentAnalytics',

            /*
            | Student analytics
            */
            'studentAnalytics',
            'topStudents',
            'studentsNeedingAttention',

            /*
            | Assignment attention
            */
            'assignmentsNeedingAttention',

            /*
            | Charts
            */
            'submissionTrend',
            'engagementData',

            /*
            | Activity
            */
            'activities',
        );
    }
}
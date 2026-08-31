<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function dashboard(Request $request): View
    {
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Active Courses
        |--------------------------------------------------------------------------
        |
        | Only courses where this student has an active enrollment.
        |
        */

        $courses = $student
            ->activeCourses()
            ->with([
                'subject',
                'schoolClass',
            ])
            ->withCount('resources')
            ->latest('courses.created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Course IDs
        |--------------------------------------------------------------------------
        */

        $courseIds = $courses->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Published Assignments
        |--------------------------------------------------------------------------
        |
        | Only show assignments:
        |
        | 1. From courses the student is actively enrolled in
        | 2. That have been published
        |
        */

        $assignments = Assignment::query()
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->with([
                'course.subject',
                'course.schoolClass',
            ])
            ->with([
                'submissions' => function ($query) use ($student) {
                    $query
                        ->where('student_id', $student->id)
                        ->latest('attempt_number');
                },
            ])
            ->latest('published_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        $now = now();

        $sevenDaysFromNow = now()->addDays(7);

        /*
        |--------------------------------------------------------------------------
        | Due Soon Assignments
        |--------------------------------------------------------------------------
        |
        | Assignments due from now through the next 7 days.
        |
        */

        $dueSoonAssignments = $assignments
            ->filter(function ($assignment) use ($now, $sevenDaysFromNow) {

                if (!$assignment->due_at) {
                    return false;
                }

                return $assignment->due_at->gte($now)
                    && $assignment->due_at->lte($sevenDaysFromNow);
            })
            ->sortBy('due_at')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Submitted Assignment IDs
        |--------------------------------------------------------------------------
        |
        | An assignment is considered completed when the student has a
        | submission with status "submitted" or "graded".
        |
        */

        $submittedAssignmentIds = $assignments
            ->filter(function ($assignment) {

                return $assignment->submissions->contains(function ($submission) {
                    return in_array(
                        $submission->status,
                        ['submitted', 'graded'],
                        true
                    );
                });
            })
            ->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Pending Assignments
        |--------------------------------------------------------------------------
        */

        $pendingAssignments = $assignments
            ->reject(function ($assignment) {

                return $assignment->submissions->contains(function ($submission) {
                    return in_array(
                        $submission->status,
                        ['submitted', 'graded'],
                        true
                    );
                });
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Submitted Assignments
        |--------------------------------------------------------------------------
        */

        $submittedAssignments = $assignments
            ->filter(function ($assignment) {

                return $assignment->submissions->contains(function ($submission) {
                    return in_array(
                        $submission->status,
                        ['submitted', 'graded'],
                        true
                    );
                });
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Graded Assignments
        |--------------------------------------------------------------------------
        */

        $gradedAssignments = $assignments
            ->filter(function ($assignment) {

                return $assignment->submissions->contains(function ($submission) {
                    return $submission->status === 'graded';
                });
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Assignment Counts
        |--------------------------------------------------------------------------
        */

        $totalAssignments = $assignments->count();

        $completedAssignments = $submittedAssignments->count();

        $pendingAssignmentCount = $pendingAssignments->count();

        $submittedAssignmentCount = $submittedAssignments->count();

        $gradedAssignmentCount = $gradedAssignments->count();

        /*
        |--------------------------------------------------------------------------
        | Assignment Progress
        |--------------------------------------------------------------------------
        */

        $assignmentProgress = $totalAssignments > 0
            ? round(($completedAssignments / $totalAssignments) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Upcoming Assignments
        |--------------------------------------------------------------------------
        |
        | Show the next five assignments with a future due date.
        |
        */

        $upcomingAssignments = $assignments
            ->filter(function ($assignment) use ($now) {

                return $assignment->due_at
                    && $assignment->due_at->gte($now);
            })
            ->sortBy('due_at')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Recent Assignment Activity
        |--------------------------------------------------------------------------
        |
        | Student's latest assignment submissions.
        |
        */

        $recentSubmissions = AssignmentSubmission::query()
            ->where('student_id', $student->id)
            ->whereHas('assignment', function ($query) use ($courseIds) {
                $query
                    ->whereIn('course_id', $courseIds)
                    ->where('is_published', true);
            })
            ->with([
                'assignment.course.subject',
                'assignment.course.schoolClass',
            ])
            ->latest('updated_at')
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Published Resources
        |--------------------------------------------------------------------------
        |
        | Resources recently published in the student's active courses.
        |
        */

        $recentResources = Resource::query()
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->with([
                'course.subject',
                'course.schoolClass',
            ])
            ->latest('published_at')
            ->limit(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view('student.dashboard', [
            'student' => $student,

            // Courses
            'courses' => $courses,

            // Assignments
            'assignments' => $assignments,
            'dueSoonAssignments' => $dueSoonAssignments,
            'upcomingAssignments' => $upcomingAssignments,
            'pendingAssignments' => $pendingAssignments,
            'submittedAssignments' => $submittedAssignments,
            'gradedAssignments' => $gradedAssignments,

            // Assignment counts
            'totalAssignments' => $totalAssignments,
            'completedAssignments' => $completedAssignments,
            'pendingAssignmentCount' => $pendingAssignmentCount,
            'submittedAssignmentCount' => $submittedAssignmentCount,
            'gradedAssignmentCount' => $gradedAssignmentCount,

            // Progress
            'assignmentProgress' => $assignmentProgress,

            // Activity
            'recentSubmissions' => $recentSubmissions,
            'recentResources' => $recentResources,
        ]);
    }
}
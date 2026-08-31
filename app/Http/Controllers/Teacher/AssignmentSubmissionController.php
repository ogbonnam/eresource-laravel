<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    /**
     * Review a specific student submission / attempt.
     */
    public function review(
        Request $request,
        Course $course,
        Assignment $assignment,
        AssignmentSubmission $submission
    ) {
        $teacher = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $course->teacher_id === $teacher->id,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Assignment must belong to this course
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $assignment->course_id === $course->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Submission must belong to this assignment
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $submission->assignment_id === $assignment->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Load assignment
        |--------------------------------------------------------------------------
        */

        $assignment->load([
            'course.subject',
            'course.schoolClass',
            'course.teacher',
            'attachments',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Load current submission
        |--------------------------------------------------------------------------
        */

        $submission->load([
            'student',
            'files',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Load complete attempt history
        |--------------------------------------------------------------------------
        */

        $attempts = AssignmentSubmission::query()
            ->with([
                'student',
                'files',
            ])
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $submission->student_id)
            ->orderBy('attempt_number')
            ->get();

        return view(
            'teacher.assignments.submissions.review',
            compact(
                'course',
                'assignment',
                'submission',
                'attempts'
            )
        );
    }


    /**
     * Download a student's submitted file.
     */
    public function downloadSubmissionFile(
        Request $request,
        Course $course,
        Assignment $assignment,
        AssignmentSubmissionFile $file
    ) {
        $teacher = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Security: course belongs to teacher
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $course->teacher_id === $teacher->id,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Load submission
        |--------------------------------------------------------------------------
        */

        $submission = $file->submission;

        abort_unless(
            $submission,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Submission belongs to assignment
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $submission->assignment_id === $assignment->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | File must actually belong to this submission
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $file->assignment_submission_id === $submission->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Verify the file exists
        |--------------------------------------------------------------------------
        */

        $disk = $file->disk ?? 'private';

        abort_unless(
            Storage::disk($disk)->exists($file->file_path),
            404,
            'Submitted file could not be found.'
        );

        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        return Storage::disk($disk)->download(
            $file->file_path,
            $file->original_name
        );
    }


    /**
     * Save grade and teacher feedback.
     */
    public function grade(
        Request $request,
        Course $course,
        Assignment $assignment,
        AssignmentSubmission $submission
    ) {
        $teacher = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $course->teacher_id === $teacher->id,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Assignment must belong to course
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $assignment->course_id === $course->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Submission must belong to assignment
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $submission->assignment_id === $assignment->id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Validate grade
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'grade' => [
                'required',
                'numeric',
                'min:0',
                'max:' . ($assignment->total_marks ?? 0),
            ],

            'feedback' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save grade
        |--------------------------------------------------------------------------
        */

        $submission->update([
            'grade' => $validated['grade'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => 'graded',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Return to review page
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'teacher.courses.assignments.submissions.review',
                [
                    'course' => $course,
                    'assignment' => $assignment,
                    'submission' => $submission,
                ]
            )
            ->with(
                'success',
                'Submission graded successfully.'
            );
    }
}
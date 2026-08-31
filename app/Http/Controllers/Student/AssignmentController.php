<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Show all published assignments available to the student.
     */
    public function index(Request $request)
    {
        $student = $request->user();

        $assignments = Assignment::query()
            ->with([
                'course.subject',
                'course.schoolClass',
            ])
            ->withCount([
                'submissions as my_submissions_count' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
            ])
            ->where('is_published', true)
            ->whereHas('course.students', function ($query) use ($student) {
                $query
                    ->where('users.id', $student->id)
                    ->where('enrollments.status', 'active');
            })
            ->orderByRaw(
                'CASE WHEN due_at IS NULL THEN 1 ELSE 0 END'
            )
            ->orderBy('due_at')
            ->latest('published_at')
            ->get();

        return view(
            'student.assignments.index',
            compact('assignments')
        );
    }


    /**
     * Show an assignment.
     *
     * Handles:
     *
     * /student/assignments/2
     *
     * /student/assignments/2?new_attempt=1
     */
    public function show(
        Request $request,
        Assignment $assignment
    ) {
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Make sure student can access this assignment
        |--------------------------------------------------------------------------
        */

        $this->ensureStudentCanAccess(
            $student,
            $assignment
        );

        /*
        |--------------------------------------------------------------------------
        | Load assignment information
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
        | Load ALL attempts belonging to this student
        |--------------------------------------------------------------------------
        |
        | This is important because the student may have:
        |
        | Attempt 1 -> Graded
        | Attempt 2 -> Draft
        |
        | The current attempt and the graded attempt must therefore be
        | treated separately.
        |--------------------------------------------------------------------------
        */

        $attempts = AssignmentSubmission::query()
            ->with([
                'files',
                'grader',
            ])
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->orderByDesc('attempt_number')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Start a new attempt
        |--------------------------------------------------------------------------
        |
        | If ?new_attempt=1 is supplied:
        |
        | - Continue an existing draft if one exists.
        | - Otherwise create the next attempt.
        |
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('new_attempt')) {

            /*
            |--------------------------------------------------------------------------
            | Find an existing draft first
            |--------------------------------------------------------------------------
            */

            $existingDraft = $attempts
                ->first(
                    fn ($attempt) =>
                        $attempt->status === 'draft'
                );

            if ($existingDraft) {

                /*
                |------------------------------------------------------------------
                | Continue existing draft.
                |------------------------------------------------------------------
                */

                $submission = $existingDraft;

            } else {

                /*
                |------------------------------------------------------------------
                | Find highest attempt number.
                |------------------------------------------------------------------
                */

                $latestAttempt = $attempts->first();

                $nextAttemptNumber =
                    ((int) ($latestAttempt?->attempt_number ?? 0)) + 1;

                /*
                |------------------------------------------------------------------
                | Check whether another attempt is allowed.
                |------------------------------------------------------------------
                */

                abort_if(
                    !$this->canStartNewAttempt(
                        $assignment,
                        $attempts
                    ),
                    403,
                    'You have reached the maximum number of attempts allowed for this assignment.'
                );

                /*
                |------------------------------------------------------------------
                | Create new draft attempt.
                |------------------------------------------------------------------
                */

                $submission = AssignmentSubmission::create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'attempt_number' => $nextAttemptNumber,
                    'status' => 'draft',
                    'content' => null,
                    'submitted_at' => null,
                    'is_late' => false,
                    'grade' => null,
                    'feedback' => null,
                    'graded_by' => null,
                    'graded_at' => null,
                ]);

                /*
                |------------------------------------------------------------------
                | Load relationships.
                |------------------------------------------------------------------
                */

                $submission->load([
                    'files',
                    'grader',
                ]);

                /*
                |------------------------------------------------------------------
                | Add new attempt to history.
                |------------------------------------------------------------------
                */

                $attempts->prepend($submission);
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | Normal page load
            |--------------------------------------------------------------------------
            |
            | The latest attempt is the current attempt.
            |
            | Example:
            |
            | Attempt 2 -> Draft
            | Attempt 1 -> Graded
            |
            | $submission = Attempt 2
            |--------------------------------------------------------------------------
            */

            $submission = $attempts->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Load current submission relationships
        |--------------------------------------------------------------------------
        */

        if ($submission) {

            $submission->load([
                'files',
                'grader',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Find the latest graded attempt
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Do NOT use $submission->feedback here.
        |
        | $submission may be a draft.
        |
        | We specifically find the latest graded attempt so the student
        | can still see teacher feedback even when a newer attempt is
        | currently a draft.
        |--------------------------------------------------------------------------
        */

        $gradedSubmission = $attempts
            ->filter(
                fn ($attempt) =>
                    $attempt->status === 'graded'
            )
            ->sortByDesc('attempt_number')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | If the current collection did not contain the graded attempt,
        | retrieve it directly from the database.
        |--------------------------------------------------------------------------
        |
        | This provides an additional safety check.
        |--------------------------------------------------------------------------
        */

        if (!$gradedSubmission) {

            $gradedSubmission = AssignmentSubmission::query()
                ->with([
                    'files',
                    'grader',
                ])
                ->where('assignment_id', $assignment->id)
                ->where('student_id', $student->id)
                ->where('status', 'graded')
                ->orderByDesc('attempt_number')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate whether another attempt can be started
        |--------------------------------------------------------------------------
        */

        $canStartNewAttempt = $this->canStartNewAttempt(
            $assignment,
            $attempts
        );

        /*
        |--------------------------------------------------------------------------
        | Determine whether current attempt can be edited
        |--------------------------------------------------------------------------
        */

        $canEditCurrentAttempt =
            $submission &&
            $submission->status === 'draft';

        /*
        |--------------------------------------------------------------------------
        | Determine whether current attempt can be submitted
        |--------------------------------------------------------------------------
        */

        $canSubmitCurrentAttempt =
            $submission &&
            $submission->status === 'draft' &&
            $this->canSubmitNow($assignment);

        /*
        |--------------------------------------------------------------------------
        | Deadline information
        |--------------------------------------------------------------------------
        */

        $isPastDue =
            $assignment->due_at &&
            now()->greaterThan($assignment->due_at);

        $lateSubmissionAllowed =
            $isPastDue &&
            $assignment->allow_late_submission &&
            (
                !$assignment->late_submission_until ||
                now()->lessThanOrEqualTo(
                    $assignment->late_submission_until
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Return student assignment page
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | gradedSubmission is now explicitly sent to the Blade.
        |--------------------------------------------------------------------------
        */

        return view(
            'student.assignments.show',
            compact(
                'assignment',
                'submission',
                'attempts',
                'gradedSubmission',
                'canStartNewAttempt',
                'canEditCurrentAttempt',
                'canSubmitCurrentAttempt',
                'isPastDue',
                'lateSubmissionAllowed'
            )
        );
    }


    /**
     * Save the current attempt as a draft.
     */
    public function saveDraft(
        Request $request,
        Assignment $assignment
    ) {
        $student = $request->user();

        $this->ensureStudentCanAccess(
            $student,
            $assignment
        );

        $validated = $request->validate([
            'attempt_id' => [
                'nullable',
                'integer',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'files' => [
                'nullable',
                'array',
                'max:10',
            ],

            'files.*' => [
                'file',
                'mimes:pdf,doc,docx',
                'max:20480',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find student's attempt
        |--------------------------------------------------------------------------
        */

        $submission = $this->getStudentAttempt(
            $student->id,
            $assignment->id,
            $validated['attempt_id'] ?? null
        );

        /*
        |--------------------------------------------------------------------------
        | If no attempt exists, create Attempt 1
        |--------------------------------------------------------------------------
        */

        if (!$submission) {

            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'attempt_number' => 1,
                'status' => 'draft',
                'content' => null,
                'submitted_at' => null,
                'is_late' => false,
                'grade' => null,
                'feedback' => null,
                'graded_by' => null,
                'graded_at' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Only drafts can be edited
        |--------------------------------------------------------------------------
        */

        abort_if(
            $submission->status !== 'draft',
            403,
            'This attempt has already been submitted and cannot be changed.'
        );

        /*
        |--------------------------------------------------------------------------
        | Save written answer
        |--------------------------------------------------------------------------
        */

        $submission->content =
            $validated['content'] ?? null;

        $submission->save();

        /*
        |--------------------------------------------------------------------------
        | Save uploaded files
        |--------------------------------------------------------------------------
        */

        $this->storeSubmissionFiles(
            $request,
            $submission
        );

        return redirect()
            ->route(
                'student.assignments.show',
                $assignment
            )
            ->with(
                'success',
                'Your draft has been saved.'
            );
    }


    /**
     * Submit the current attempt.
     */
    public function submit(
        Request $request,
        Assignment $assignment
    ) {
        $student = $request->user();

        $this->ensureStudentCanAccess(
            $student,
            $assignment
        );

        $validated = $request->validate([
            'attempt_id' => [
                'required',
                'integer',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'files' => [
                'nullable',
                'array',
                'max:10',
            ],

            'files.*' => [
                'file',
                'mimes:pdf,doc,docx',
                'max:20480',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find exact attempt belonging to this student
        |--------------------------------------------------------------------------
        */

        $submission = AssignmentSubmission::query()
            ->where('id', $validated['attempt_id'])
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->with('files')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Only drafts can be submitted
        |--------------------------------------------------------------------------
        */

        abort_if(
            $submission->status !== 'draft',
            403,
            'This attempt has already been submitted.'
        );

        /*
        |--------------------------------------------------------------------------
        | Save latest editor content
        |--------------------------------------------------------------------------
        */

        $submission->content =
            $validated['content'] ?? $submission->content;

        /*
        |--------------------------------------------------------------------------
        | Save newly selected files
        |--------------------------------------------------------------------------
        */

        $this->storeSubmissionFiles(
            $request,
            $submission
        );

        /*
        |--------------------------------------------------------------------------
        | Check whether there is something to submit
        |--------------------------------------------------------------------------
        */

        $content = trim(
            $submission->content ?? ''
        );

        $hasWrittenAnswer =
            strip_tags($content) !== '';

        $hasExistingFiles =
            $submission->files()->exists();

        $hasNewFiles =
            $request->hasFile('files');

        if (
            !$hasWrittenAnswer &&
            !$hasExistingFiles &&
            !$hasNewFiles
        ) {
            return back()
                ->withErrors([
                    'content' =>
                        'Please enter an answer or attach a file before submitting.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Check deadline
        |--------------------------------------------------------------------------
        */

        if (
            $assignment->due_at &&
            now()->greaterThan($assignment->due_at)
        ) {

            /*
            |--------------------------------------------------------------------------
            | Late submissions not allowed
            |--------------------------------------------------------------------------
            */

            if (!$assignment->allow_late_submission) {

                return back()
                    ->withErrors([
                        'submission' =>
                            'This assignment is past its deadline and late submissions are not allowed.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Late submission deadline passed
            |--------------------------------------------------------------------------
            */

            if (
                $assignment->late_submission_until &&
                now()->greaterThan(
                    $assignment->late_submission_until
                )
            ) {

                return back()
                    ->withErrors([
                        'submission' =>
                            'The final deadline for late submissions has passed.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Mark this attempt as late
            |--------------------------------------------------------------------------
            */

            $submission->is_late = true;

        } else {

            $submission->is_late = false;
        }

        /*
        |--------------------------------------------------------------------------
        | Finalize attempt
        |--------------------------------------------------------------------------
        */

        $submission->status = 'submitted';

        $submission->submitted_at = now();

        $submission->save();

        return redirect()
            ->route(
                'student.assignments.show',
                $assignment
            )
            ->with(
                'success',
                'Attempt ' .
                $submission->attempt_number .
                ' has been submitted successfully.'
            );
    }


    /**
     * Remove a file from a draft attempt.
     */
    public function removeFile(
        Request $request,
        AssignmentSubmissionFile $file
    ) {
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Get submission
        |--------------------------------------------------------------------------
        */

        $submission = $file->submission;

        /*
        |--------------------------------------------------------------------------
        | Make sure this belongs to the logged-in student
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $submission->student_id === $student->id,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Only drafts can be modified
        |--------------------------------------------------------------------------
        */

        abort_if(
            $submission->status !== 'draft',
            403,
            'Submitted attempts cannot be modified.'
        );

        /*
        |--------------------------------------------------------------------------
        | Delete physical file
        |--------------------------------------------------------------------------
        */

        if (
            Storage::disk('private')
                ->exists($file->file_path)
        ) {

            Storage::disk('private')
                ->delete($file->file_path);
        }

        /*
        |--------------------------------------------------------------------------
        | Delete database record
        |--------------------------------------------------------------------------
        */

        $file->delete();

        return back()
            ->with(
                'success',
                'Attachment removed.'
            );
    }


    /**
     * Open/download a teacher attachment.
     */
    public function downloadAttachment(
            Request $request,
            Assignment $assignment,
            $attachment
        ) {
            $student = $request->user();

            /*
            |--------------------------------------------------------------------------
            | Make sure student can access this assignment
            |--------------------------------------------------------------------------
            */

            $this->ensureStudentCanAccess(
                $student,
                $assignment
            );

            /*
            |--------------------------------------------------------------------------
            | Find the attachment belonging to this assignment
            |--------------------------------------------------------------------------
            */

            $attachment = $assignment
                ->attachments()
                ->where('id', $attachment)
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Teacher assignment attachments are stored on the PUBLIC disk.
            |--------------------------------------------------------------------------
            */

            $disk = Storage::disk('public');

            /*
            |--------------------------------------------------------------------------
            | Make sure the physical file exists.
            |--------------------------------------------------------------------------
            */

            abort_unless(
                $disk->exists($attachment->file_path),
                404,
                'Attachment file not found.'
            );

            /*
            |--------------------------------------------------------------------------
            | Get the physical path
            |--------------------------------------------------------------------------
            */

            $path = $disk->path(
                $attachment->file_path
            );

            /*
            |--------------------------------------------------------------------------
            | Return the file inline.
            |
            | PDF files will open in the browser.
            | Other files can be downloaded by the browser.
            |--------------------------------------------------------------------------
            */

            return response()->file(
                $path,
                [
                    'Content-Type' =>
                        $attachment->mime_type
                        ?: 'application/octet-stream',

                    'Content-Disposition' =>
                        'inline; filename="' .
                        addslashes(
                            $attachment->original_name
                        ) .
                        '"',
                ]
            );
        }


    /**
     * Get one specific student attempt.
     */
    private function getStudentAttempt(
        int $studentId,
        int $assignmentId,
        ?int $attemptId = null
    ): ?AssignmentSubmission {

        $query = AssignmentSubmission::query()
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId);

        /*
        |--------------------------------------------------------------------------
        | Specific attempt requested
        |--------------------------------------------------------------------------
        */

        if ($attemptId) {

            $query->where(
                'id',
                $attemptId
            );

        } else {

            /*
            |--------------------------------------------------------------------------
            | Otherwise get latest attempt
            |--------------------------------------------------------------------------
            */

            $query->orderByDesc(
                'attempt_number'
            );
        }

        return $query
            ->with([
                'files',
                'grader',
            ])
            ->first();
    }


    /**
     * Determine whether another attempt can be started.
     */
    private function canStartNewAttempt(
        Assignment $assignment,
        $attempts
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Resubmissions must be enabled
        |--------------------------------------------------------------------------
        */

        if (!$assignment->allow_resubmission) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Respect maximum attempts
        |--------------------------------------------------------------------------
        */

        $attemptCount = $attempts->count();

        if (
            $assignment->max_attempts &&
            $attemptCount >= $assignment->max_attempts
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Don't create another attempt while a draft exists
        |--------------------------------------------------------------------------
        */

        if (
            $attempts->contains(
                fn ($attempt) =>
                    $attempt->status === 'draft'
            )
        ) {
            return false;
        }

        return true;
    }


    /**
     * Determine whether the current attempt can be submitted now.
     */
    private function canSubmitNow(
        Assignment $assignment
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | No due date
        |--------------------------------------------------------------------------
        */

        if (!$assignment->due_at) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Before due date
        |--------------------------------------------------------------------------
        */

        if (
            now()->lessThanOrEqualTo(
                $assignment->due_at
            )
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Late submissions disabled
        |--------------------------------------------------------------------------
        */

        if (!$assignment->allow_late_submission) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Late deadline passed
        |--------------------------------------------------------------------------
        */

        if (
            $assignment->late_submission_until &&
            now()->greaterThan(
                $assignment->late_submission_until
            )
        ) {
            return false;
        }

        return true;
    }


    /**
     * Store uploaded student files.
     */
    private function storeSubmissionFiles(
        Request $request,
        AssignmentSubmission $submission
    ): void {

        if (!$request->hasFile('files')) {
            return;
        }

        foreach (
            $request->file('files') as $file
        ) {

            $path = $file->store(
                'assignment-submissions/' .
                $submission->id,
                'private'
            );

            AssignmentSubmissionFile::create([
                'assignment_submission_id' =>
                    $submission->id,

                'original_name' =>
                    $file->getClientOriginalName(),

                'file_path' =>
                    $path,

                'mime_type' =>
                    $file->getClientMimeType(),

                'file_size' =>
                    $file->getSize(),
            ]);
        }
    }


    /**
     * Make sure the student is allowed to access
     * this assignment.
     */
    private function ensureStudentCanAccess(
        $student,
        Assignment $assignment
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Assignment must be published
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $assignment->is_published,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Student must be actively enrolled in course
        |--------------------------------------------------------------------------
        */

        $isEnrolled = $assignment
            ->course
            ->students()
            ->where(
                'users.id',
                $student->id
            )
            ->where(
                'enrollments.status',
                'active'
            )
            ->exists();

        abort_unless(
            $isEnrolled,
            403,
            'You are not enrolled in this course.'
        );
    }
}
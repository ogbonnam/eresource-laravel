<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentAttachment;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /**
     * Display assignments for a course.
     */
    public function index(
        Request $request,
        Course $course
    ): View {
        $this->authorizeCourse($request, $course);

        $assignments = $course->assignments()
            ->with([
                'creator',
                'attachments',
            ])
            ->latest()
            ->get();

        return view('teacher.assignments.index', [
            'course' => $course,
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show assignment creation form.
     */
    public function create(
        Request $request,
        Course $course
    ): View {
        $this->authorizeCourse($request, $course);

        return view('teacher.assignments.create', [
            'course' => $course,
        ]);
    }

    /**
     * Store a new assignment.
     */
    public function store(
        Request $request,
        Course $course
    ): RedirectResponse {
        $this->authorizeCourse($request, $course);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'instructions' => [
                'nullable',
                'string',
            ],

            'total_marks' => [
                'required',
                'integer',
                'min:1',
                'max:100000',
            ],

            'due_at' => [
                'nullable',
                'date',
            ],

            /*
             * ---------------------------------------------------------
             * Late submissions
             * ---------------------------------------------------------
             */
            'allow_late_submission' => [
                'nullable',
                'boolean',
            ],

            'late_submission_until' => [
                'nullable',
                'date',
                'after_or_equal:due_at',
            ],

            /*
             * ---------------------------------------------------------
             * Resubmissions
             * ---------------------------------------------------------
             */
            'allow_resubmission' => [
                'nullable',
                'boolean',
            ],

            'max_attempts' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            /*
             * ---------------------------------------------------------
             * Publishing
             * ---------------------------------------------------------
             */
            'is_published' => [
                'nullable',
                'boolean',
            ],

            /*
             * ---------------------------------------------------------
             * Attachments
             * ---------------------------------------------------------
             */
            'attachments' => [
                'nullable',
                'array',
                'max:20',
            ],

            'attachments.*' => [
                'file',
                'max:51200', // 50 MB per file
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,jpg,jpeg,png,gif,webp',
            ],
        ]);

        $allowLateSubmission = $request->boolean(
            'allow_late_submission'
        );

        $allowResubmission = $request->boolean(
            'allow_resubmission'
        );

        $isPublished = $request->boolean(
            'is_published'
        );

        /*
         * If late submissions are disabled,
         * there should be no late-submission deadline.
         */
        $lateSubmissionUntil = $allowLateSubmission
            ? ($validated['late_submission_until'] ?? null)
            : null;

        /*
         * If resubmissions are disabled,
         * force exactly one attempt.
         */
        $maxAttempts = $allowResubmission
            ? ($validated['max_attempts'] ?? 2)
            : 1;

        /*
         * -------------------------------------------------------------
         * Create assignment and attachments in one transaction.
         * -------------------------------------------------------------
         */
        $assignment = DB::transaction(function () use (
            $request,
            $course,
            $validated,
            $allowLateSubmission,
            $lateSubmissionUntil,
            $allowResubmission,
            $maxAttempts,
            $isPublished
        ) {
            $assignment = $course->assignments()->create([
                'created_by' => $request->user()->id,

                'title' => $validated['title'],

                'description' =>
                    $validated['description'] ?? null,

                'instructions' =>
                    $validated['instructions'] ?? null,

                'total_marks' =>
                    $validated['total_marks'],

                'due_at' =>
                    $validated['due_at'] ?? null,

                'allow_late_submission' =>
                    $allowLateSubmission,

                'late_submission_until' =>
                    $lateSubmissionUntil,

                'allow_resubmission' =>
                    $allowResubmission,

                'max_attempts' =>
                    $maxAttempts,

                'is_published' =>
                    $isPublished,

                'published_at' =>
                    $isPublished
                        ? now()
                        : null,
            ]);

            /*
             * Save uploaded assignment attachments.
             */
            $this->storeAttachments(
                $request,
                $assignment
            );

            return $assignment;
        });

        return redirect()
            ->route(
                'teacher.courses.assignments.show',
                [
                    'course' => $course,
                    'assignment' => $assignment,
                ]
            )
            ->with(
                'success',
                'Assignment created successfully.'
            );
    }

    /**
     * Display an assignment.
     */
    public function show(
        Request $request,
        Course $course,
        Assignment $assignment
    ): View {
        $this->authorizeCourse($request, $course);

        $this->ensureAssignmentBelongsToCourse(
            $course,
            $assignment
        );

        $assignment->load([
            'creator',
            'attachments',
            'submissions.student',
        ]);

        return view('teacher.assignments.show', [
            'course' => $course,
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show assignment edit form.
     */
    public function edit(
        Request $request,
        Course $course,
        Assignment $assignment
    ): View {
        $this->authorizeCourse($request, $course);

        $this->ensureAssignmentBelongsToCourse(
            $course,
            $assignment
        );

        $assignment->load('attachments');

        return view('teacher.assignments.edit', [
            'course' => $course,
            'assignment' => $assignment,
        ]);
    }

    /**
     * Update assignment.
     */
    public function update(
        Request $request,
        Course $course,
        Assignment $assignment
    ): RedirectResponse {
        $this->authorizeCourse($request, $course);

        $this->ensureAssignmentBelongsToCourse(
            $course,
            $assignment
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'instructions' => [
                'nullable',
                'string',
            ],

            'total_marks' => [
                'required',
                'integer',
                'min:1',
                'max:100000',
            ],

            'due_at' => [
                'nullable',
                'date',
            ],

            /*
             * ---------------------------------------------------------
             * Late submissions
             * ---------------------------------------------------------
             */
            'allow_late_submission' => [
                'nullable',
                'boolean',
            ],

            'late_submission_until' => [
                'nullable',
                'date',
                'after_or_equal:due_at',
            ],

            /*
             * ---------------------------------------------------------
             * Resubmissions
             * ---------------------------------------------------------
             */
            'allow_resubmission' => [
                'nullable',
                'boolean',
            ],

            'max_attempts' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            /*
             * ---------------------------------------------------------
             * Publishing
             * ---------------------------------------------------------
             */
            'is_published' => [
                'nullable',
                'boolean',
            ],

            /*
             * ---------------------------------------------------------
             * New attachments
             * ---------------------------------------------------------
             */
            'attachments' => [
                'nullable',
                'array',
                'max:20',
            ],

            'attachments.*' => [
                'file',
                'max:51200',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,jpg,jpeg,png,gif,webp',
            ],

            /*
             * ---------------------------------------------------------
             * Attachments to remove
             * ---------------------------------------------------------
             */
            'remove_attachments' => [
                'nullable',
                'array',
            ],

            'remove_attachments.*' => [
                'integer',
                'exists:assignment_attachments,id',
            ],
        ]);

        $wasPublished = (bool) $assignment->is_published;

        $isPublished = $request->boolean(
            'is_published'
        );

        $allowLateSubmission = $request->boolean(
            'allow_late_submission'
        );

        $allowResubmission = $request->boolean(
            'allow_resubmission'
        );

        /*
         * If late submissions are disabled,
         * clear the deadline.
         */
        $lateSubmissionUntil = $allowLateSubmission
            ? ($validated['late_submission_until'] ?? null)
            : null;

        /*
         * If resubmissions are disabled,
         * force one attempt.
         */
        $maxAttempts = $allowResubmission
            ? ($validated['max_attempts'] ?? 2)
            : 1;

        DB::transaction(function () use (
            $request,
            $assignment,
            $validated,
            $allowLateSubmission,
            $lateSubmissionUntil,
            $allowResubmission,
            $maxAttempts,
            $isPublished,
            $wasPublished
        ) {
            /*
             * ---------------------------------------------------------
             * Update assignment
             * ---------------------------------------------------------
             */
            $assignment->title =
                $validated['title'];

            $assignment->description =
                $validated['description'] ?? null;

            $assignment->instructions =
                $validated['instructions'] ?? null;

            $assignment->total_marks =
                $validated['total_marks'];

            $assignment->due_at =
                $validated['due_at'] ?? null;

            $assignment->allow_late_submission =
                $allowLateSubmission;

            $assignment->late_submission_until =
                $lateSubmissionUntil;

            $assignment->allow_resubmission =
                $allowResubmission;

            $assignment->max_attempts =
                $maxAttempts;

            $assignment->is_published =
                $isPublished;

            /*
             * Only set published_at when the assignment
             * changes from unpublished to published.
             */
            if (!$wasPublished && $isPublished) {
                $assignment->published_at = now();
            }

            /*
             * Clear published_at if unpublished.
             */
            if (!$isPublished) {
                $assignment->published_at = null;
            }

            $assignment->save();

            /*
             * ---------------------------------------------------------
             * Remove selected existing attachments.
             * ---------------------------------------------------------
             */
            $removeAttachments =
                $validated['remove_attachments'] ?? [];

            if (!empty($removeAttachments)) {
                $attachments =
                    AssignmentAttachment::query()
                        ->where('assignment_id', $assignment->id)
                        ->whereIn('id', $removeAttachments)
                        ->get();

                foreach ($attachments as $attachment) {
                    $this->deleteAttachmentFile(
                        $attachment
                    );

                    $attachment->delete();
                }
            }

            /*
             * ---------------------------------------------------------
             * Add newly uploaded attachments.
             * ---------------------------------------------------------
             */
            $this->storeAttachments(
                $request,
                $assignment
            );
        });

        return redirect()
            ->route(
                'teacher.courses.assignments.show',
                [
                    'course' => $course,
                    'assignment' => $assignment,
                ]
            )
            ->with(
                'success',
                'Assignment updated successfully.'
            );
    }

    /**
     * Delete assignment.
     *
     * All attached files are removed from storage
     * before the assignment is deleted.
     */
    public function destroy(
        Request $request,
        Course $course,
        Assignment $assignment
    ): RedirectResponse {
        $this->authorizeCourse($request, $course);

        $this->ensureAssignmentBelongsToCourse(
            $course,
            $assignment
        );

        DB::transaction(function () use ($assignment) {
            $assignment->load('attachments');

            foreach ($assignment->attachments as $attachment) {
                $this->deleteAttachmentFile(
                    $attachment
                );

                $attachment->delete();
            }

            $assignment->delete();
        });

        return redirect()
            ->route(
                'teacher.courses.assignments.index',
                $course
            )
            ->with(
                'success',
                'Assignment deleted successfully.'
            );
    }

    /**
     * Store uploaded assignment attachments.
     */
    protected function storeAttachments(
        Request $request,
        Assignment $assignment
    ): void {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach (
            $request->file('attachments')
            as $file
        ) {
            if (!$file->isValid()) {
                continue;
            }

            /*
             * Create a unique directory for each assignment.
             */
            $directory =
                'assignments/' .
                $assignment->id;

            /*
             * Generate a safe unique filename.
             */
            $extension =
                strtolower(
                    $file->getClientOriginalExtension()
                );

            $fileName =
                Str::uuid()->toString() .
                ($extension
                    ? '.' . $extension
                    : '');

            /*
             * Store on the public disk.
             *
             * Files will be stored under:
             *
             * storage/app/public/assignments/{assignment_id}/
             */
            $filePath = $file->storeAs(
                $directory,
                $fileName,
                'public'
            );

            /*
             * Create database attachment record.
             */
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $assignment->attachments()->create([
                        'uploaded_by' => $request->user()->id,
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $file->store(
                            'assignments/' . $assignment->id,
                            'public'
                        ),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }
    }

    /**
     * Delete an attachment's physical file.
     */
    protected function deleteAttachmentFile(
        AssignmentAttachment $attachment
    ): void {
        if (
            $attachment->file_path &&
            Storage::disk('public')->exists(
                $attachment->file_path
            )
        ) {
            Storage::disk('public')->delete(
                $attachment->file_path
            );
        }
    }

    /**
     * Verify that the authenticated teacher owns the course.
     */
    protected function authorizeCourse(
        Request $request,
        Course $course
    ): void {
        abort_unless(
            $request->user()->isTeacher()
            &&
            $course->teacher_id ===
                $request->user()->id,
            403,
            'You are not authorized to manage this course.'
        );
    }

    /**
     * Verify that the assignment belongs to the specified course.
     */
    protected function ensureAssignmentBelongsToCourse(
        Course $course,
        Assignment $assignment
    ): void {
        abort_unless(
            $assignment->course_id ===
                $course->id,
            404
        );
    }
}
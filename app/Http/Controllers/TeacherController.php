<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Resource;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use App\Models\Broadcast;
use App\Models\BroadcastAttachment;
use App\Models\BroadcastRecipient;
use Illuminate\Support\Facades\Storage;

use App\Models\BroadcastView;

class TeacherController extends Controller
{
    /**
     * Teacher Dashboard
     */
    public function dashboard()
    {
        $teacher = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | TEACHER COURSES
        |--------------------------------------------------------------------------
        */

        $courses = Course::query()
            ->where('teacher_id', $teacher->id)
            ->with([
                'subject',
                'schoolClass',
            ])
            ->withCount([
                'resources',
            ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | COURSE IDS
        |--------------------------------------------------------------------------
        */

        $courseIds = $courses->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | STUDENT COUNT
        |--------------------------------------------------------------------------
        |
        | Count unique active students across all teacher courses.
        |
        */

        $studentCount = 0;

        if ($courseIds->isNotEmpty()) {

            $studentCount = Course::query()
                ->whereIn('id', $courseIds)
                ->with('students')
                ->get()
                ->pluck('students')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | RESOURCES
        |--------------------------------------------------------------------------
        */

        $resources = collect();

        if ($courseIds->isNotEmpty()) {

            $resources = Resource::query()
                ->whereIn('course_id', $courseIds)
                ->with([
                    'course',
                ])
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENTS
        |--------------------------------------------------------------------------
        |
        | Assignments come from the assignments table.
        |
        */

        $assignments = collect();

        if ($courseIds->isNotEmpty()) {

            $assignments = Assignment::query()
                ->whereIn('course_id', $courseIds)
                ->with([
                    'course',
                ])
                ->latest()
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | BASIC STATISTICS
        |--------------------------------------------------------------------------
        */

        $classCount = $courses->count();

        $resourceCount = $resources->count();

        /*
         * Published assignments.
         */

        $assignmentCount = $assignments
            ->where('is_published', true)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT SUBMISSIONS
        |--------------------------------------------------------------------------
        */

        $submissions = collect();

        if ($assignments->isNotEmpty()) {

            $assignmentIds = $assignments->pluck('id');

            $submissions = AssignmentSubmission::query()
                ->whereIn(
                    'assignment_id',
                    $assignmentIds
                )
                ->with([
                    'assignment.course',
                    'student',
                    'grader',
                ])
                ->latest('updated_at')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | PENDING GRADING
        |--------------------------------------------------------------------------
        |
        | Submitted = waiting for teacher to grade.
        |
        */

        $pendingGradingCount = $submissions
            ->where('status', 'submitted')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | GRADED SUBMISSIONS
        |--------------------------------------------------------------------------
        */

        $gradedSubmissionCount = $submissions
            ->where('status', 'graded')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RECENT RESOURCES
        |--------------------------------------------------------------------------
        */

        $recentResources = $resources
            ->sortByDesc('updated_at')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RECENT ASSIGNMENTS
        |--------------------------------------------------------------------------
        */

        $recentAssignments = $assignments
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RECENT STUDENT ACTIVITY
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | This variable is deliberately named
        | $recentStudentActivity because that is what the Blade uses.
        |
        */

        $recentStudentActivity = $submissions
            ->sortByDesc(function ($submission) {

                return $submission->submitted_at
                    ?? $submission->updated_at;

            })
            ->take(8)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | AVAILABLE CLASSES AND SUBJECTS
        |--------------------------------------------------------------------------
        |
        | These are used by the teacher course create/edit pages.
        |
        | A teacher can select a class, and only subjects assigned to
        | that class will be available.
        |
        */

        $classes = SchoolClass::query()
            ->where('is_active', true)
            ->with([
                'subjects' => function ($query) {
                    $query
                        ->where('subjects.is_active', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN TEACHER DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'teacher.dashboard',
            [

                /*
                | Teacher
                */

                'teacher' =>
                    $teacher,

                /*
                | Courses
                */

                'courses' =>
                    $courses,

                /*
                | Main statistics
                */

                'classCount' =>
                    $classCount,

                'studentCount' =>
                    $studentCount,

                'resourceCount' =>
                    $resourceCount,

                'assignmentCount' =>
                    $assignmentCount,

                /*
                | Grading
                */

                'pendingGradingCount' =>
                    $pendingGradingCount,

                'gradedSubmissionCount' =>
                    $gradedSubmissionCount,

                /*
                | Recent content
                */

                'recentResources' =>
                    $recentResources,

                'recentAssignments' =>
                    $recentAssignments,

                /*
                | Student activity
                */

                'recentStudentActivity' =>
                    $recentStudentActivity,

                /*
                | Available classes and subjects
                */

                'classes' =>
                    $classes,
            ]
        );
    }


    /**
     * Show the course creation form.
     */
    public function createCourse(): View
    {
        $classes = SchoolClass::query()
            ->where('is_active', true)
            ->with([
                'subjects' => function ($query) {
                    $query
                        ->where('subjects.is_active', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('teacher.courses.create', [
            'classes' => $classes,
        ]);
    }


    /**
     * Store a new teacher course.
     */
    public function storeCourse(Request $request)
    {
        $teacher = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
                function ($attribute, $value, $fail) use ($request) {

                    $exists = DB::table('class_subject')
                        ->where('class_id', $request->class_id)
                        ->where('subject_id', $value)
                        ->exists();

                    if (! $exists) {
                        $fail(
                            'The selected subject is not available for this class.'
                        );
                    }
                },
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE COURSE
        |--------------------------------------------------------------------------
        |
        | The teacher automatically becomes the course teacher.
        |
        */

        $course = Course::create([
            'teacher_id' => $teacher->id,
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with(
                'success',
                'Course created successfully.'
            );
    }

        /**
     * Teacher broadcast inbox.
     */
    public function broadcasts(): View
    {
        $teacher = Auth::user();

        abort_unless(
            $teacher && $teacher->role === 'teacher',
            403
        );

        $broadcasts = Broadcast::query()
            ->whereNotNull('sent_at')
            ->whereHas('recipients', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with([
                'sender',
                'department',
                'attachments',
                'recipients' => function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                },
            ])
            ->latest('sent_at')
            ->paginate(15);

        return view(
            'teacher.broadcasts.index',
            [
                'broadcasts' => $broadcasts,
            ]
        );
    }


    /**
     * Show one broadcast and mark it as read.
     */
    public function showBroadcast(Broadcast $broadcast): View
{
        $teacher = Auth::user();

        abort_unless(
            $teacher && $teacher->role === 'teacher',
            403
        );

        abort_if(
            is_null($broadcast->sent_at),
            404
        );

        $recipient = BroadcastRecipient::query()
            ->where('broadcast_id', $broadcast->id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $broadcast->load([
            'sender',
            'department',
            'attachments',
        ]);

        $now = now();

        /*
        * -------------------------------------------------------------
        * Update the recipient summary.
        * -------------------------------------------------------------
        */

        $recipient->increment('open_count');

        $recipient->update([
            'first_opened_at' => $recipient->first_opened_at ?? $now,
            'last_opened_at' => $now,
            'read_at' => $recipient->read_at ?? $now,
        ]);

        /*
        * -------------------------------------------------------------
        * Store an individual view event.
        *
        * Every time the teacher opens the broadcast, one record is
        * created here.
        * -------------------------------------------------------------
        */

        BroadcastView::create([
            'broadcast_id' => $broadcast->id,
            'teacher_id' => $teacher->id,
            'viewed_at' => $now,
        ]);

        /*
        * -------------------------------------------------------------
        * Refresh the recipient so the view receives the new count.
        * -------------------------------------------------------------
        */

        $recipient->refresh();

        return view(
            'teacher.broadcasts.show',
            [
                'broadcast' => $broadcast,
                'recipient' => $recipient,
            ]
        );
    }


    /**
     * Open a broadcast attachment.
     */
    public function broadcastAttachment(
        BroadcastAttachment $attachment
    )
    {
        $teacher = Auth::user();

        abort_unless(
            $teacher && $teacher->role === 'teacher',
            403
        );

        /*
        |----------------------------------------------------------------------
        | SECURITY CHECK
        |----------------------------------------------------------------------
        |
        | The teacher must actually be a recipient of the broadcast
        | that owns this attachment.
        |
        */

        BroadcastRecipient::query()
            ->where(
                'broadcast_id',
                $attachment->broadcast_id
            )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->firstOrFail();

        /*
        |----------------------------------------------------------------------
        | CHECK FILE
        |----------------------------------------------------------------------
        */

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($attachment->file_path),
            404
        );

        /*
        |----------------------------------------------------------------------
        | OPEN FILE INLINE
        |----------------------------------------------------------------------
        */

        return response()->file(
            $disk->path($attachment->file_path),
            [
                'Content-Type' =>
                    $attachment->mime_type
                    ?: 'application/octet-stream',

                'Content-Disposition' =>
                    'inline; filename="' .
                    addslashes($attachment->file_name) .
                    '"',
            ]
        );
    }


    /**
     * Open the Google Sheet attached to a broadcast.
     */
    public function broadcastSheet(Broadcast $broadcast)
    {
        $teacher = Auth::user();

        abort_unless(
            $teacher && $teacher->role === 'teacher',
            403
        );

        /*
        |----------------------------------------------------------------------
        | SECURITY CHECK
        |----------------------------------------------------------------------
        */

        BroadcastRecipient::query()
            ->where(
                'broadcast_id',
                $broadcast->id
            )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->firstOrFail();

        /*
        |----------------------------------------------------------------------
        | CHECK GOOGLE SHEET
        |----------------------------------------------------------------------
        */

        abort_unless(
            filled($broadcast->google_sheet_url),
            404
        );

        /*
        |----------------------------------------------------------------------
        | REDIRECT
        |----------------------------------------------------------------------
        */

        return redirect()->away(
            $broadcast->google_sheet_url
        );
    }


    /**
     * Acknowledge a broadcast.
     */
    public function acknowledgeBroadcast(
        Broadcast $broadcast
    )
    {
        $teacher = Auth::user();

        abort_unless(
            $teacher && $teacher->role === 'teacher',
            403
        );

        $recipient = BroadcastRecipient::query()
            ->where(
                'broadcast_id',
                $broadcast->id
            )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->firstOrFail();

        $recipient->update([
            'acknowledged_at' => now(),
        ]);

        return redirect()
            ->route(
                'teacher.broadcasts.show',
                $broadcast
            )
            ->with(
                'success',
                'Broadcast acknowledged successfully.'
            );
    }
}

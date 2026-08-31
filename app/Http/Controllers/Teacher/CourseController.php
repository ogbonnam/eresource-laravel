<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display courses owned by the authenticated teacher.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()
            ->teachingCourses()
            ->with([
                'subject',
                'schoolClass',
            ])
            ->withCount('resources')
            ->latest('courses.created_at')
            ->get();

        return view('teacher.courses.index', [
            'courses' => $courses,
        ]);
    }


    /**
     * Show the create course form.
     *
     * The teacher selects a class first.
     * Only subjects assigned to that class are then available.
     */
    public function create(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHORISATION
        |--------------------------------------------------------------------------
        */

        Gate::authorize('create', Course::class);


        /*
        |--------------------------------------------------------------------------
        | ACTIVE CLASSES WITH THEIR ACTIVE SUBJECTS
        |--------------------------------------------------------------------------
        |
        | This is the important part for the new Class -> Subject system.
        |
        | Example:
        |
        | SS2
        |   - Mathematics
        |   - English
        |   - Physics
        |   - Chemistry
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
        | CLASS -> SUBJECT DATA FOR JAVASCRIPT
        |--------------------------------------------------------------------------
        |
        | The create Blade uses:
        |
        |     @json($classSubjects)
        |
        | Therefore this variable MUST be supplied by this controller.
        |
        */

        $classSubjects = $classes
            ->mapWithKeys(function (SchoolClass $class) {

                return [
                    $class->id => $class->subjects
                        ->map(function (Subject $subject) {

                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                            ];

                        })
                        ->values()
                        ->toArray(),
                ];

            })
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | CREATE VIEW
        |--------------------------------------------------------------------------
        */

        return view('teacher.courses.create', [
            'classes' => $classes,
            'classSubjects' => $classSubjects,
        ]);
    }


    /**
     * Store a newly created course.
     */
    public function store(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHORISATION
        |--------------------------------------------------------------------------
        */

        Gate::authorize('create', Course::class);


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        |
        | There is deliberately NO course code anymore.
        |
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | VERIFY CLASS IS ACTIVE
        |--------------------------------------------------------------------------
        */

        $class = SchoolClass::query()
            ->where('id', $validated['class_id'])
            ->where('is_active', true)
            ->first();

        if (! $class) {

            return back()
                ->withInput()
                ->withErrors([
                    'class_id' => 'The selected class is not active.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY SUBJECT IS ACTIVE
        |--------------------------------------------------------------------------
        */

        $subject = Subject::query()
            ->where('id', $validated['subject_id'])
            ->where('is_active', true)
            ->first();

        if (! $subject) {

            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' => 'The selected subject is not active.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY SUBJECT BELONGS TO CLASS
        |--------------------------------------------------------------------------
        |
        | This is the server-side protection.
        |
        | Even if somebody manipulates the HTML/JavaScript and submits:
        |
        |     SS2 + Biology
        |
        | when Biology is not assigned to SS2, the course will NOT be created.
        |
        */

        $subjectBelongsToClass = DB::table('class_subject')
            ->where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if (! $subjectBelongsToClass) {

            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' =>
                        'The selected subject is not available for the selected class.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE COURSE
        |--------------------------------------------------------------------------
        |
        | The logged-in teacher automatically becomes the teacher.
        |
        */

        $course = Course::create([
            'teacher_id' => $request->user()->id,
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
     * Display a teacher's course.
     */
    public function show(
        Request $request,
        Course $course
    ): View {

        /*
        |--------------------------------------------------------------------------
        | AUTHORISATION
        |--------------------------------------------------------------------------
        |
        | Uses CoursePolicy::view().
        |
        */

        Gate::authorize('view', $course);


        /*
        |--------------------------------------------------------------------------
        | LOAD COURSE DATA
        |--------------------------------------------------------------------------
        */

        $course->load([
            'subject',
            'schoolClass',
            'resources',
        ]);


        /*
        |--------------------------------------------------------------------------
        | DISPLAY COURSE
        |--------------------------------------------------------------------------
        */

        return view('teacher.courses.show', [
            'course' => $course,
        ]);
    }


    /**
     * Show the edit course form.
     */
    public function edit(
        Request $request,
        Course $course
    ): View {

        /*
        |--------------------------------------------------------------------------
        | AUTHORISATION
        |--------------------------------------------------------------------------
        */

        Gate::authorize('update', $course);


        /*
        |--------------------------------------------------------------------------
        | ACTIVE CLASSES WITH ACTIVE SUBJECTS
        |--------------------------------------------------------------------------
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
        | CLASS -> SUBJECT DATA
        |--------------------------------------------------------------------------
        */

        $classSubjects = $classes
            ->mapWithKeys(function (SchoolClass $class) {

                return [
                    $class->id => $class->subjects
                        ->map(function (Subject $subject) {

                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                            ];

                        })
                        ->values()
                        ->toArray(),
                ];

            })
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | EDIT VIEW
        |--------------------------------------------------------------------------
        */

        return view('teacher.courses.edit', [
            'course' => $course,
            'classes' => $classes,
            'classSubjects' => $classSubjects,
        ]);
    }


    /**
     * Update a course.
     */
    public function update(
        Request $request,
        Course $course
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | AUTHORISATION
        |--------------------------------------------------------------------------
        */

        Gate::authorize('update', $course);


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        |
        | Course code has been removed.
        |
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | VERIFY CLASS IS ACTIVE
        |--------------------------------------------------------------------------
        */

        $class = SchoolClass::query()
            ->where('id', $validated['class_id'])
            ->where('is_active', true)
            ->first();

        if (! $class) {

            return back()
                ->withInput()
                ->withErrors([
                    'class_id' => 'The selected class is not active.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY SUBJECT IS ACTIVE
        |--------------------------------------------------------------------------
        */

        $subject = Subject::query()
            ->where('id', $validated['subject_id'])
            ->where('is_active', true)
            ->first();

        if (! $subject) {

            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' => 'The selected subject is not active.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY SUBJECT BELONGS TO CLASS
        |--------------------------------------------------------------------------
        */

        $subjectBelongsToClass = DB::table('class_subject')
            ->where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if (! $subjectBelongsToClass) {

            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' =>
                        'The selected subject is not available for the selected class.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE COURSE
        |--------------------------------------------------------------------------
        |
        | teacher_id is intentionally NOT updated.
        |
        */

        $course->update([
            'name' => $validated['name'],
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? $course->is_active,
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
                'Course updated successfully.'
            );
    }


    /**
     * Delete a course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHORISATION
        |--------------------------------------------------------------------------
        */

        $teacher = auth()->user();

        abort_unless(
            $course->teacher_id === $teacher->id,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | DELETE RESOURCE FILES
        |--------------------------------------------------------------------------
        */

        foreach ($course->resources as $resource) {

            if ($resource->file_path) {

                Storage::disk('public')
                    ->delete($resource->file_path);
            }

            /*
             * Resource content/images can be cleaned separately
             * if required later.
             */
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE COURSE RESOURCES
        |--------------------------------------------------------------------------
        */

        $course->resources()->delete();


        /*
        |--------------------------------------------------------------------------
        | DELETE COURSE
        |--------------------------------------------------------------------------
        */

        $course->delete();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('teacher.courses.index')
            ->with(
                'success',
                'Course and its resources were deleted successfully.'
            );
    }
}

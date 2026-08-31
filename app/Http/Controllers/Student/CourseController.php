<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display the student's active/enrolled courses.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()
            ->activeCourses()
            ->with([
                'subject',
                'schoolClass',
            ])
            ->withCount('resources')
            ->latest('courses.created_at')
            ->get();

        return view('student.courses.index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Display one course.
     *
     * Only students with an ACTIVE enrollment
     * can access the course.
     */
    public function show(Request $request, Course $course): View
    {
        $student = $request->user();

        /*
         * Check that this student has an active enrollment
         * for this specific course.
         */
        $isEnrolled = $student
            ->activeCourses()
            ->where('courses.id', $course->id)
            ->exists();

        if (! $isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        /*
         * Load the course information and only
         * published resources.
         */
        $course->load([
            'subject',
            'schoolClass',
            'resources' => function ($query) {
                $query
                    ->where('is_published', true)
                    ->orderBy('sort_order');
            },
        ]);

        return view('student.courses.show', [
            'course' => $course,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    /**
     * Show the Join Course page.
     */
    public function create()
    {
        return view('student.courses.join');
    }

    /**
     * Submit a course enrollment request.
     *
     * The student enters the enrollment code generated
     * for the course by the system.
     *
     * The request is created as "pending".
     * The teacher must approve it before the student
     * gets access to the course.
     */
    public function store(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'enrollment_code' => [
                'required',
                'string',
                'max:20',
            ],
        ], [
            'enrollment_code.required' =>
                'Please enter the course enrollment code.',
        ]);

        $code = strtoupper(
            trim($validated['enrollment_code'])
        );

        /*
         * Find the active course using the enrollment code.
         */
        $course = Course::query()
            ->where('enrollment_code', $code)
            ->where('is_active', true)
            ->first();

        if (! $course) {
            return back()
                ->withInput()
                ->withErrors([
                    'enrollment_code' =>
                        'No active course was found with that enrollment code.',
                ]);
        }

        /*
         * Check whether this student already has
         * an enrollment record for this course.
         */
        $existingEnrollment = Enrollment::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        /*
         * Student is already approved.
         */
        if ($existingEnrollment?->status === 'active') {
            return back()->with(
                'error',
                'You are already enrolled in this course.'
            );
        }

        /*
         * Student already has a pending request.
         */
        if ($existingEnrollment?->status === 'pending') {
            return back()->with(
                'error',
                'You already have a pending request for this course.'
            );
        }

        /*
         * Student previously completed the course.
         */
        if ($existingEnrollment?->status === 'completed') {
            return back()->with(
                'error',
                'You have already completed this course.'
            );
        }

        /*
         * If the previous enrollment was dropped,
         * reuse the existing enrollment record.
         *
         * This is important because the enrollments table
         * has a unique constraint on user_id + course_id.
         */
        if ($existingEnrollment?->status === 'dropped') {
            $existingEnrollment->update([
                'status' => 'pending',
                'enrolled_at' => null,
            ]);

            return redirect()
                ->route('student.courses.join')
                ->with(
                    'success',
                    "Your request to join {$course->name} has been sent to the teacher."
                );
        }

        /*
         * No previous enrollment exists.
         *
         * Create a new pending request.
         */
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'enrolled_at' => null,
        ]);

        return redirect()
            ->route('student.courses.join')
            ->with(
                'success',
                "Your request to join {$course->name} has been sent to the teacher."
            );
    }
}
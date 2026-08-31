<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseStudentController extends Controller
{
    /**
     * Display students enrolled in the course.
     *
     * Only active students are shown here.
     */
    public function index(Course $course): View
    {
        $this->authorizeTeacher($course);

        $enrollments = $course->enrollments()
            ->where('status', 'active')
            ->with('user')
            ->latest('enrolled_at')
            ->get();

        return view('teacher.courses.students.index', [
            'course' => $course,
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Show the Add Student page.
     *
     * This is for manually adding an existing student.
     */
    public function create(Course $course): View
    {
        $this->authorizeTeacher($course);

        $enrolledStudentIds = $course->enrollments()
            ->whereIn('status', ['active', 'pending'])
            ->pluck('user_id');

        $students = User::query()
            ->where('role', 'student')
            ->whereNotIn('id', $enrolledStudentIds)
            ->orderBy('name')
            ->get();

        return view('teacher.courses.students.create', [
            'course' => $course,
            'students' => $students,
        ]);
    }

    /**
     * Manually enroll a student.
     *
     * This immediately creates an active enrollment because
     * the teacher is explicitly adding the student.
     */
    public function store(
        Request $request,
        Course $course
    ): RedirectResponse {
        $this->authorizeTeacher($course);

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $student = User::query()
            ->where('id', $validated['student_id'])
            ->where('role', 'student')
            ->firstOrFail();

        $existingEnrollment = $course->enrollments()
            ->where('user_id', $student->id)
            ->first();

        /*
         * If the student already has an active enrollment,
         * do not create another one.
         */
        if ($existingEnrollment?->status === 'active') {
            return back()->with(
                'error',
                'This student is already enrolled in this course.'
            );
        }

        /*
         * If the student has a pending request, the teacher
         * can approve it through the pending requests page.
         */
        if ($existingEnrollment?->status === 'pending') {
            return back()->with(
                'error',
                'This student already has a pending enrollment request.'
            );
        }

        /*
         * Reuse a dropped enrollment because the enrollments
         * table has a unique user_id + course_id constraint.
         */
        if ($existingEnrollment?->status === 'dropped') {
            $existingEnrollment->update([
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
        } else {
            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
        }

        return redirect()
            ->route('teacher.courses.students.index', $course)
            ->with(
                'success',
                "{$student->name} has been enrolled successfully."
            );
    }

    /**
     * Display pending enrollment requests.
     */
    public function pending(Course $course): View
    {
        $this->authorizeTeacher($course);

        $enrollments = $course->enrollments()
            ->where('status', 'pending')
            ->with('user')
            ->latest('created_at')
            ->get();

        return view('teacher.courses.students.pending', [
            'course' => $course,
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Approve a student's enrollment request.
     */
    public function approve(
        Course $course,
        Enrollment $enrollment
    ): RedirectResponse {
        $this->authorizeTeacher($course);

        /*
         * Make absolutely sure the enrollment belongs
         * to this course.
         */
        abort_unless(
            $enrollment->course_id === $course->id,
            404
        );

        /*
         * Only pending requests can be approved.
         */
        if ($enrollment->status !== 'pending') {
            return back()->with(
                'error',
                'This enrollment request is no longer pending.'
            );
        }

        $enrollment->update([
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $studentName = $enrollment->user?->name ?? 'Student';

        return back()->with(
            'success',
            "{$studentName} has been approved for {$course->name}."
        );
    }

    /**
     * Reject a student's enrollment request.
     *
     * We use "dropped" rather than deleting the enrollment
     * so that the enrollment history is preserved.
     */
    public function reject(
        Course $course,
        Enrollment $enrollment
    ): RedirectResponse {
        $this->authorizeTeacher($course);

        /*
         * Make absolutely sure the enrollment belongs
         * to this course.
         */
        abort_unless(
            $enrollment->course_id === $course->id,
            404
        );

        /*
         * Only pending requests can be rejected.
         */
        if ($enrollment->status !== 'pending') {
            return back()->with(
                'error',
                'This enrollment request is no longer pending.'
            );
        }

        $studentName = $enrollment->user?->name ?? 'Student';

        $enrollment->update([
            'status' => 'dropped',
            'enrolled_at' => null,
        ]);

        return back()->with(
            'success',
            "{$studentName}'s enrollment request has been rejected."
        );
    }

    /**
     * Remove a student from the course.
     *
     * We do not delete the enrollment because we want
     * to preserve enrollment history.
     */
    public function destroy(
        Course $course,
        User $student
    ): RedirectResponse {
        $this->authorizeTeacher($course);

        $enrollment = $course->enrollments()
            ->where('user_id', $student->id)
            ->where('status', 'active')
            ->firstOrFail();

        $enrollment->update([
            'status' => 'dropped',
        ]);

        return redirect()
            ->route('teacher.courses.students.index', $course)
            ->with(
                'success',
                "{$student->name} has been removed from this course."
            );
    }

    /**
     * Ensure the authenticated teacher owns the course.
     */
    private function authorizeTeacher(Course $course): void
    {
        abort_unless(
            $course->teacher_id === auth()->id(),
            403,
            'You are not authorized to manage this course.'
        );
    }
}
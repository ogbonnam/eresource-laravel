<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\YearGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YearGroupEnrollmentController extends Controller
{
    /**
     * The year-group form is displayed on the existing Join Course page.
     */
    public function create()
    {
        return redirect()->route('student.courses.join');
    }

    /**
     * Enroll a student in all active courses belonging
     * to the classes assigned to a year group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'access_code' => [
                'required',
                'string',
                'max:30',
            ],
        ], [
            'access_code.required' =>
                'Please enter your year-group access code.',
        ]);

        $code = strtoupper(
            preg_replace('/\s+/', '', trim($validated['access_code']))
        );

        $yearGroup = YearGroup::query()
            ->where('access_code', $code)
            ->where('is_active', true)
            ->first();

        if (! $yearGroup) {
            return back()
                ->withInput()
                ->withErrors([
                    'access_code' =>
                        'The year-group code is invalid or inactive.',
                ]);
        }

        // Get all active courses in all active classes
        // assigned to this year group.
        $courses = $yearGroup->classes()
            ->where('is_active', true)
            ->with([
                'courses' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->get()
            ->flatMap(function ($schoolClass) {
                return $schoolClass->courses;
            })
            ->unique('id')
            ->values();

        if ($courses->isEmpty()) {
            return back()->with(
                'error',
                "No active courses are currently available for {$yearGroup->name}."
            );
        }

        $student = $request->user();

        $requireApproval = $yearGroup->require_approval;

        $result = DB::transaction(function () use (
            $courses,
            $student,
            $requireApproval
        ) {
            $created = 0;
            $activated = 0;
            $alreadyActive = 0;
            $pending = 0;
            $completed = 0;

            foreach ($courses as $course) {

                $enrollment = Enrollment::query()
                    ->where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->lockForUpdate()
                    ->first();

                $targetStatus = $requireApproval
                    ? 'pending'
                    : 'active';

                $enrolledAt = $requireApproval
                    ? null
                    : now();

                // Preserve completed courses.
                if ($enrollment?->status === 'completed') {
                    $completed++;
                    continue;
                }

                // Preserve existing active enrollments.
                if ($enrollment?->status === 'active') {
                    $alreadyActive++;
                    continue;
                }

                // Existing pending request.
                if ($enrollment?->status === 'pending') {

                    if ($requireApproval) {
                        $pending++;
                        continue;
                    }

                    // If approval is disabled for this year group,
                    // activate an existing pending enrollment.
                    $enrollment->update([
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);

                    $activated++;

                    continue;
                }

                // Reactivate dropped enrollments or create new ones.
                if ($enrollment) {
                    $enrollment->update([
                        'status' => $targetStatus,
                        'enrolled_at' => $enrolledAt,
                    ]);

                    if ($targetStatus === 'active') {
                        $activated++;
                    } else {
                        $pending++;
                    }

                    continue;
                }

                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => $targetStatus,
                    'enrolled_at' => $enrolledAt,
                ]);

                $created++;
            }

            return [
                'created' => $created,
                'activated' => $activated,
                'already_active' => $alreadyActive,
                'pending' => $pending,
                'completed' => $completed,
            ];
        });

        $total = $courses->count();

        if ($requireApproval) {
            $message = "Your {$yearGroup->name} enrollment request has been processed. "
                . "{$result['created']} new course request(s) submitted, "
                . "{$result['pending']} already pending, "
                . "{$result['already_active']} already active, "
                . "and {$result['completed']} completed course(s) preserved. "
                . "Your teacher must approve pending requests.";
        } else {
            $message = "You have successfully joined the available courses "
                . "for {$yearGroup->name}. "
                . "{$result['created']} new enrollment(s) created, "
                . "{$result['activated']} previous enrollment(s) reactivated, "
                . "{$result['already_active']} already active, "
                . "and {$result['completed']} completed course(s) preserved.";
        }

        return redirect()
            ->route('student.courses.join')
            ->with('success', $message);
    }
}

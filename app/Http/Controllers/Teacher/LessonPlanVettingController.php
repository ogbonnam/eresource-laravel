<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonPlanVettingController extends Controller
{
    /**
     * Show lesson plans awaiting vetting.
     */
    public function index(): View
    {
        $user = auth()->user();

        $this->ensureVetter($user);

        $lessonPlans = LessonPlan::query()
            ->with([
                'teacher',
                'faculty',
                'subject',
                'schoolClass',
                'vetter',
            ])
            ->where('faculty_id', $user->faculty_id)
            ->where('status', 'pending')
            ->latest('submitted_at')
            ->latest('id')
            ->paginate(15);

        return view(
            'teacher.lesson-plan-vetting.index',
            compact('lessonPlans')
        );
    }


    /**
     * Show a single lesson plan for vetting.
     */
    public function show(LessonPlan $lessonPlan): View
    {
        $user = auth()->user();

        $this->ensureVetter($user);
        $this->ensureSameFaculty($lessonPlan);

        $lessonPlan->load([
            'teacher',
            'faculty',
            'subject',
            'schoolClass',
            'vetter',
        ]);

        return view(
            'teacher.lesson-plan-vetting.show',
            compact('lessonPlan')
        );
    }


    /**
     * Show all approved lesson plans in the HOD/HOF's faculty.
     */
    public function approved(): View
    {
        $user = auth()->user();

        $this->ensureVetter($user);

        $lessonPlans = LessonPlan::query()
            ->with([
                'teacher',
                'faculty',
                'subject',
                'schoolClass',
                'vetter',
            ])
            ->where('faculty_id', $user->faculty_id)
            ->where('status', 'approved')
            ->latest('approved_at')
            ->latest('id')
            ->paginate(15);

        return view(
            'teacher.lesson-plan-vetting.approved',
            compact('lessonPlans')
        );
    }


    /**
     * Approve a lesson plan.
     */
    public function approve(
        Request $request,
        LessonPlan $lessonPlan
    ): RedirectResponse {
        $user = auth()->user();

        $this->ensureVetter($user);
        $this->ensureSameFaculty($lessonPlan);

        abort_unless(
            $lessonPlan->status === 'pending',
            403,
            'This lesson plan is no longer awaiting vetting.'
        );

        $validated = $request->validate([
            'vetter_comment' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $lessonPlan->update([
            'status' => 'approved',
            'vetter_comment' => $validated['vetter_comment'] ?? null,
            'vetted_by' => $user->id,
            'vetted_at' => now(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('teacher.lesson-plan-vetting.index')
            ->with(
                'success',
                'Lesson plan approved successfully.'
            );
    }


    /**
     * Request changes from the teacher.
     */
    public function requestRevision(
        Request $request,
        LessonPlan $lessonPlan
    ): RedirectResponse {
        $user = auth()->user();

        $this->ensureVetter($user);
        $this->ensureSameFaculty($lessonPlan);

        abort_unless(
            $lessonPlan->status === 'pending',
            403,
            'This lesson plan is no longer awaiting vetting.'
        );

        $validated = $request->validate([
            'vetter_comment' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $lessonPlan->update([
            'status' => 'revision_requested',
            'vetter_comment' => $validated['vetter_comment'],
            'vetted_by' => $user->id,
            'vetted_at' => now(),
            'approved_at' => null,
        ]);

        return redirect()
            ->route('teacher.lesson-plan-vetting.index')
            ->with(
                'success',
                'Revision has been requested from the teacher.'
            );
    }


    /**
     * Reject a lesson plan.
     */
    public function reject(
        Request $request,
        LessonPlan $lessonPlan
    ): RedirectResponse {
        $user = auth()->user();

        $this->ensureVetter($user);
        $this->ensureSameFaculty($lessonPlan);

        abort_unless(
            $lessonPlan->status === 'pending',
            403,
            'This lesson plan is no longer awaiting vetting.'
        );

        $validated = $request->validate([
            'vetter_comment' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $lessonPlan->update([
            'status' => 'rejected',
            'vetter_comment' => $validated['vetter_comment'],
            'vetted_by' => $user->id,
            'vetted_at' => now(),
            'approved_at' => null,
        ]);

        return redirect()
            ->route('teacher.lesson-plan-vetting.index')
            ->with(
                'success',
                'Lesson plan rejected.'
            );
    }


    /**
     * Ensure the logged-in user is an HOD or HOF.
     */
    protected function ensureVetter($user): void
    {
        abort_unless(
            $user
            && $user->role === 'teacher'
            && in_array(
                $user->staff_position,
                ['hod', 'hof'],
                true
            )
            && $user->faculty_id,
            403
        );
    }


    /**
     * Ensure the lesson plan belongs to the same faculty.
     */
    protected function ensureSameFaculty(
        LessonPlan $lessonPlan
    ): void {
        abort_unless(
            $lessonPlan->faculty_id === auth()->user()->faculty_id,
            403,
            'You cannot vet lesson plans from another faculty.'
        );
    }
}
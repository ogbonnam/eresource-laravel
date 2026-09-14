<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LessonPlanController extends Controller
{
    /**
     * Display the teacher's lesson plans.
     */
    public function index(): View
    {
        $user = auth()->user();

        $lessonPlans = LessonPlan::query()
            ->with([
                'subject',
                'schoolClass',
                'faculty',
                'vetter',
            ])
            ->where('teacher_id', $user->id)
            ->latest('lesson_date')
            ->latest('id')
            ->paginate(15);

        return view(
            'teacher.lesson-plans.index',
            compact('lessonPlans')
        );
    }

    /**
     * Show the create lesson plan form.
     */
    public function create(): View
    {
        $user = auth()->user();

        /*
         * We use the teacher's existing Course assignments
         * to determine which class/subject combinations they teach.
         */
        $assignments = Course::query()
            ->with(['subject', 'schoolClass'])
            ->where('teacher_id', $user->id)
            ->where('is_active', true)
            ->get();

        $classes = $assignments
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $subjects = $assignments
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view(
            'teacher.lesson-plans.create',
            compact('classes', 'subjects')
        );
    }

    /**
     * Store a new lesson plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_unless(
            $user->role === 'teacher',
            403
        );

        abort_unless(
            $user->faculty_id,
            403,
            'You must be assigned to a faculty before creating a lesson plan.'
        );

        $validated = $request->validate([
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

            'week' => [
                'nullable',
                'integer',
                'min:1',
                'max:52',
            ],

            'lesson_date' => [
                'nullable',
                'date',
            ],

            'topic' => [
                'required',
                'string',
                'max:255',
            ],

            'objectives' => [
                'nullable',
                'string',
            ],

            'activities' => [
                'nullable',
                'string',
            ],

            'assessment' => [
                'nullable',
                'string',
            ],

            'teacher_comment' => [
                'nullable',
                'string',
            ],

            'file_path' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ]);

        /*
         * Make sure the teacher actually teaches
         * this Class + Subject combination.
         */
        $this->ensureTeacherAssignment(
            $user->id,
            $validated['class_id'],
            $validated['subject_id']
        );

        $validated['teacher_id'] = $user->id;
        $validated['faculty_id'] = $user->faculty_id;
        $validated['status'] = 'draft';

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request
                ->file('file_path')
                ->store('lesson-plans', 'public');
        }

        LessonPlan::create($validated);

        return redirect()
            ->route('teacher.lesson-plans.index')
            ->with(
                'success',
                'Lesson plan saved as draft.'
            );
    }

    /**
     * Show the edit lesson plan form.
     */
    public function edit(LessonPlan $lessonPlan): View
    {
        $this->authorizeTeacherOwnership($lessonPlan);

        abort_unless(
            in_array(
                $lessonPlan->status,
                ['draft', 'revision_requested'],
                true
            ),
            403,
            'This lesson plan can no longer be edited.'
        );

        $user = auth()->user();

        $assignments = Course::query()
            ->with(['subject', 'schoolClass'])
            ->where('teacher_id', $user->id)
            ->where('is_active', true)
            ->get();

        $classes = $assignments
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $subjects = $assignments
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view(
            'teacher.lesson-plans.edit',
            compact(
                'lessonPlan',
                'classes',
                'subjects'
            )
        );
    }

    /**
     * Update an existing lesson plan.
     */
    public function update(
        Request $request,
        LessonPlan $lessonPlan
    ): RedirectResponse {
        $this->authorizeTeacherOwnership($lessonPlan);

        abort_unless(
            in_array(
                $lessonPlan->status,
                ['draft', 'revision_requested'],
                true
            ),
            403,
            'This lesson plan can no longer be edited.'
        );

        $validated = $request->validate([
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

            'week' => [
                'nullable',
                'integer',
                'min:1',
                'max:52',
            ],

            'lesson_date' => [
                'nullable',
                'date',
            ],

            'topic' => [
                'required',
                'string',
                'max:255',
            ],

            'objectives' => [
                'nullable',
                'string',
            ],

            'activities' => [
                'nullable',
                'string',
            ],

            'assessment' => [
                'nullable',
                'string',
            ],

            'teacher_comment' => [
                'nullable',
                'string',
            ],

            'file_path' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ]);

        /*
         * Make sure the teacher actually teaches
         * this Class + Subject combination.
         */
        $this->ensureTeacherAssignment(
            auth()->id(),
            $validated['class_id'],
            $validated['subject_id']
        );

        if ($request->hasFile('file_path')) {

            if ($lessonPlan->file_path) {
                Storage::disk('public')
                    ->delete($lessonPlan->file_path);
            }

            $validated['file_path'] = $request
                ->file('file_path')
                ->store('lesson-plans', 'public');
        }

        $validated['teacher_id'] = auth()->id();
        $validated['faculty_id'] = auth()->user()->faculty_id;

        /*
         * A revision request becomes a fresh draft
         * when the teacher makes changes.
         */
        if ($lessonPlan->status === 'revision_requested') {

            $validated['status'] = 'draft';

            $validated['vetter_comment'] = null;
            $validated['vetted_by'] = null;
            $validated['vetted_at'] = null;
            $validated['approved_at'] = null;
        }

        $lessonPlan->update($validated);

        return redirect()
            ->route('teacher.lesson-plans.index')
            ->with(
                'success',
                'Lesson plan updated.'
            );
    }

    /**
     * Submit a lesson plan for HOD/HOF vetting.
     */
    public function submit(
        LessonPlan $lessonPlan
    ): RedirectResponse {
        $this->authorizeTeacherOwnership($lessonPlan);

        abort_unless(
            in_array(
                $lessonPlan->status,
                ['draft', 'revision_requested'],
                true
            ),
            403,
            'This lesson plan cannot be submitted.'
        );

        abort_unless(
            auth()->user()->faculty_id === $lessonPlan->faculty_id,
            403
        );

        $lessonPlan->update([
            'status' => 'pending',
            'submitted_at' => now(),
            'vetted_by' => null,
            'vetted_at' => null,
            'approved_at' => null,
        ]);

        return back()->with(
            'success',
            'Lesson plan submitted for vetting.'
        );
    }

    /**
     * Ensure the teacher owns the lesson plan.
     */
    protected function authorizeTeacherOwnership(
        LessonPlan $lessonPlan
    ): void {
        abort_unless(
            $lessonPlan->teacher_id === auth()->id(),
            403
        );
    }

    /**
     * Ensure the teacher teaches this Class + Subject combination.
     */
    protected function ensureTeacherAssignment(
        int $teacherId,
        int $classId,
        int $subjectId
    ): void {
        $assigned = Course::query()
            ->where('teacher_id', $teacherId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();

        abort_unless(
            $assigned,
            403,
            'You are not assigned to teach this class and subject.'
        );
    }
}
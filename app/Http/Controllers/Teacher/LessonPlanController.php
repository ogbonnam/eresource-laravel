<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LessonPlanController extends Controller
{
    /** Maximum number of files per lesson plan. */
    private const MAX_FILES = 20;

    /** Maximum size of one file, in kilobytes (20 MB). */
    private const MAX_FILE_KB = 20480;

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

            'files' => [
                'nullable',
                'array',
                'max:' . self::MAX_FILES,
            ],

            'files.*' => [
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,zip',
                'max:' . self::MAX_FILE_KB,
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

        $validated = Arr::except($validated, ['files']);

        $validated['teacher_id'] = $user->id;
        $validated['faculty_id'] = $user->faculty_id;
        $validated['status'] = 'draft';

        $validated['files'] = $this->storeFiles(
            $request->file('files', [])
        );

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

            'files' => [
                'nullable',
                'array',
                'max:' . self::MAX_FILES,
            ],

            'files.*' => [
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,zip',
                'max:' . self::MAX_FILE_KB,
            ],

            // Paths of existing files the teacher ticked "Remove" on
            'remove_files' => [
                'nullable',
                'array',
            ],

            'remove_files.*' => [
                'string',
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

        /*
         * FILES
         * -----
         * Keep the existing files the teacher did not remove,
         * then add any newly uploaded ones.
         */
        $remove = $validated['remove_files'] ?? [];
        $newFiles = $request->file('files', []);

        $existing = collect($lessonPlan->files ?? []);

        $removedFiles = $existing
            ->filter(fn ($file) => in_array($file['path'], $remove, true));

        $keptFiles = $existing
            ->reject(fn ($file) => in_array($file['path'], $remove, true));

        $legacyRemoved = $lessonPlan->file_path
            && in_array($lessonPlan->file_path, $remove, true);

        $legacyKept = $lessonPlan->file_path && ! $legacyRemoved ? 1 : 0;

        if ($keptFiles->count() + $legacyKept + count($newFiles) > self::MAX_FILES) {
            throw ValidationException::withMessages([
                'files' => 'A lesson plan can have at most '
                    . self::MAX_FILES
                    . ' files. Remove some files or put them in a ZIP file.',
            ]);
        }

        $validated = Arr::except($validated, ['files', 'remove_files']);

        $validated['files'] = $keptFiles
            ->values()
            ->merge($this->storeFiles($newFiles))
            ->values()
            ->all();

        if ($legacyRemoved) {
            $validated['file_path'] = null;
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

        // Remember the old legacy path before the update changes it
        $oldLegacyPath = $lessonPlan->file_path;

        $lessonPlan->update($validated);

        // Delete the removed files from disk
        foreach ($removedFiles as $file) {
            Storage::disk('public')->delete($file['path']);
        }

        if ($legacyRemoved && $oldLegacyPath) {
            Storage::disk('public')->delete($oldLegacyPath);
        }

        return redirect()
            ->route('teacher.lesson-plans.index')
            ->with(
                'success',
                'Lesson plan updated.'
            );
    }


    public function destroy(LessonPlan $lessonPlan): RedirectResponse
    {
        $this->authorizeTeacherOwnership($lessonPlan);

        abort_unless(
            in_array($lessonPlan->status, ['draft', 'revision_requested'], true),
            403,
            'Only drafts and plans needing revision can be deleted.'
        );

        // Delete the uploaded files from disk too
        foreach ($lessonPlan->all_files as $file) {
            Storage::disk('public')->delete($file['path']);
        }

        $lessonPlan->delete();

        return redirect()
            ->route('teacher.lesson-plans.index')
            ->with('success', 'Lesson plan deleted.');
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
     * Save uploaded files (same "lesson-plans" folder on the public disk
     * as before) and return the list to keep in the database.
     */
    protected function storeFiles(array $uploads): array
    {
        $saved = [];

        foreach ($uploads as $upload) {
            $saved[] = [
                'path' => $upload->store('lesson-plans', 'public'),
                'name' => $upload->getClientOriginalName(),
                'size' => $upload->getSize(),
            ];
        }

        return $saved;
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
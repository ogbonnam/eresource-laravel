@extends('layouts.dashboard')

@section('title', 'Edit Assignment · ' . $assignment->title)

@section('content')

<div class="mx-auto max-w-5xl space-y-8">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <section>

        <a
            href="{{ route('teacher.courses.assignments.show', [$course, $assignment]) }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
        >
            ← Back to Assignment
        </a>

        <div class="mt-6">

            <p class="text-sm font-medium text-slate-500">
                {{ $course->subject?->name ?? 'Course' }}
                ·
                {{ $course->name }}

                @if ($course->schoolClass)
                    · {{ $course->schoolClass->name }}
                @endif
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                Edit Assignment
            </h1>

            <p class="mt-2 text-slate-500">
                Update the assignment details, instructions, submission rules
                and attachments.
            </p>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

            <p class="font-semibold text-red-800">
                Please fix the following:
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        id="assignment-form"
        method="POST"
        action="{{ route('teacher.courses.assignments.update', [$course, $assignment]) }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @method('PUT')


        {{-- ===================================================== --}}
        {{-- BASIC INFORMATION --}}
        {{-- ===================================================== --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Assignment Information
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Update the title and description students see.
                </p>

            </div>


            <div class="space-y-6 p-6">

                {{-- Title --}}
                <div>

                    <label
                        for="title"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Assignment Title
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $assignment->title) }}"
                        required
                        maxlength="255"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >

                </div>


                {{-- Description --}}
                <div>

                    <label
                        for="description"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Short Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="5000"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >{{ old('description', $assignment->description) }}</textarea>

                </div>

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- INSTRUCTIONS --}}
        {{-- ===================================================== --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Assignment Instructions
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Update the instructions students will receive.
                </p>

            </div>


            <div class="p-6">

                <div
                    class="flex flex-wrap items-center gap-1 rounded-t-xl border border-slate-300 bg-slate-50 p-2"
                >

                    <button type="button" data-assignment-command="paragraph" class="assignment-editor-button">
                        ¶
                    </button>

                    <button type="button" data-assignment-command="heading1" class="assignment-editor-button font-bold">
                        H1
                    </button>

                    <button type="button" data-assignment-command="heading2" class="assignment-editor-button font-bold">
                        H2
                    </button>

                    <button type="button" data-assignment-command="heading3" class="assignment-editor-button font-bold">
                        H3
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-300"></div>

                    <button type="button" data-assignment-command="bold" class="assignment-editor-button font-bold">
                        B
                    </button>

                    <button type="button" data-assignment-command="italic" class="assignment-editor-button italic">
                        I
                    </button>

                    <button type="button" data-assignment-command="underline" class="assignment-editor-button underline">
                        U
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-300"></div>

                    <button type="button" data-assignment-command="bulletList" class="assignment-editor-button">
                        • List
                    </button>

                    <button type="button" data-assignment-command="orderedList" class="assignment-editor-button">
                        1. List
                    </button>

                    <button type="button" data-assignment-command="blockquote" class="assignment-editor-button">
                        Quote
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-300"></div>

                    <button type="button" data-assignment-command="alignLeft" class="assignment-editor-button">
                        ←
                    </button>

                    <button type="button" data-assignment-command="alignCenter" class="assignment-editor-button">
                        ↔
                    </button>

                    <button type="button" data-assignment-command="alignRight" class="assignment-editor-button">
                        →
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-300"></div>

                    <button type="button" data-assignment-command="link" class="assignment-editor-button">
                        Link
                    </button>

                    <button type="button" data-assignment-command="image" class="assignment-editor-button">
                        Image
                    </button>

                    <button type="button" data-assignment-command="table" class="assignment-editor-button">
                        Table
                    </button>

                    <div class="mx-1 h-6 w-px bg-slate-300"></div>

                    <button type="button" data-assignment-command="undo" class="assignment-editor-button">
                        ↶
                    </button>

                    <button type="button" data-assignment-command="redo" class="assignment-editor-button">
                        ↷
                    </button>

                </div>


                <div
                    id="assignment-editor"
                    data-image-upload-url="{{ route('teacher.courses.resources.images.store', $course) }}"
                    class="assignment-tiptap-editor min-h-[420px] rounded-b-xl border border-t-0 border-slate-300 bg-white px-6 py-5"
                >{!! old('instructions', $assignment->instructions ?? '') !!}</div>


                <textarea
                    id="assignment-instructions"
                    name="instructions"
                    class="hidden"
                >{{ old('instructions', $assignment->instructions ?? '') }}</textarea>


                <p class="mt-3 text-xs text-slate-500">
                    Use headings, lists, formatting and links to make the instructions clear for students.
                </p>

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- ASSIGNMENT SETTINGS --}}
        {{-- ===================================================== --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Assignment Settings
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Set marks, deadlines and submission rules.
                </p>

            </div>


            <div class="space-y-8 p-6">

                {{-- Marks + Due Date --}}
                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Total Marks --}}
                    <div>

                        <label
                            for="total_marks"
                            class="block text-sm font-medium text-slate-700"
                        >
                            Total Marks
                        </label>

                        <div class="relative mt-2">

                            <input
                                id="total_marks"
                                name="total_marks"
                                type="number"
                                min="1"
                                max="100000"
                                step="1"
                                value="{{ old('total_marks', $assignment->total_marks) }}"
                                required
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-20 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                            >

                            <span class="absolute inset-y-0 right-4 flex items-center text-sm text-slate-400">
                                marks
                            </span>

                        </div>

                    </div>


                    {{-- Due Date --}}
                    <div>

                        <label
                            for="due_at"
                            class="block text-sm font-medium text-slate-700"
                        >
                            Due Date & Time
                        </label>

                        <input
                            id="due_at"
                            name="due_at"
                            type="datetime-local"
                            value="{{ old('due_at', $assignment->due_at ? $assignment->due_at->format('Y-m-d\TH:i') : '') }}"
                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- LATE SUBMISSIONS --}}
                {{-- ================================================= --}}

                <div class="border-t border-slate-100 pt-6">

                    <div class="flex items-start gap-3">

                        <input
                            id="allow_late_submission"
                            name="allow_late_submission"
                            type="checkbox"
                            value="1"
                            @checked(old('allow_late_submission', $assignment->allow_late_submission))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300"
                        >

                        <div>

                            <label
                                for="allow_late_submission"
                                class="block text-sm font-semibold text-slate-900"
                            >
                                Allow late submissions
                            </label>

                            <p class="mt-1 text-sm text-slate-500">
                                Students can submit after the due date.
                            </p>

                        </div>

                    </div>


                    <div
                        id="late-submission-settings"
                        class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-5 {{ old('allow_late_submission', $assignment->allow_late_submission) ? '' : 'hidden' }}"
                    >

                        <label
                            for="late_submission_until"
                            class="block text-sm font-medium text-slate-700"
                        >
                            Late Submission Cutoff
                        </label>

                        <input
                            id="late_submission_until"
                            name="late_submission_until"
                            type="datetime-local"
                            value="{{ old('late_submission_until', $assignment->late_submission_until ? $assignment->late_submission_until->format('Y-m-d\TH:i') : '') }}"
                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Optional. Leave empty if late submissions should remain available indefinitely.
                        </p>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- RESUBMISSIONS --}}
                {{-- ================================================= --}}

                <div class="border-t border-slate-100 pt-6">

                    <div class="flex items-start gap-3">

                        <input
                            id="allow_resubmission"
                            name="allow_resubmission"
                            type="checkbox"
                            value="1"
                            @checked(old('allow_resubmission', $assignment->allow_resubmission))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300"
                        >

                        <div>

                            <label
                                for="allow_resubmission"
                                class="block text-sm font-semibold text-slate-900"
                            >
                                Allow resubmissions
                            </label>

                            <p class="mt-1 text-sm text-slate-500">
                                Students can submit another attempt after submitting.
                            </p>

                        </div>

                    </div>


                    <div
                        id="resubmission-settings"
                        class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-5 {{ old('allow_resubmission', $assignment->allow_resubmission) ? '' : 'hidden' }}"
                    >

                        <label
                            for="max_attempts"
                            class="block text-sm font-medium text-slate-700"
                        >
                            Maximum Attempts
                        </label>

                        <div class="mt-2 flex items-center gap-3">

                            <input
                                id="max_attempts"
                                name="max_attempts"
                                type="number"
                                min="1"
                                max="100"
                                step="1"
                                value="{{ old('max_attempts', $assignment->max_attempts ?? 2) }}"
                                class="block w-32 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                            >

                            <span class="text-sm text-slate-500">
                                total attempts
                            </span>

                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            This includes the student's first submission.
                        </p>

                    </div>

                </div>


                {{-- Information --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">

                    <div class="flex gap-3">

                        <div class="mt-0.5 text-blue-600">
                            ℹ
                        </div>

                        <div>

                            <p class="text-sm font-semibold text-blue-900">
                                Submission rules
                            </p>

                            <p class="mt-1 text-sm leading-6 text-blue-800">
                                Students can only submit according to the rules
                                configured here. Disabling resubmissions limits
                                each student to one attempt.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- EXISTING ATTACHMENTS --}}
        {{-- ===================================================== --}}

        @if ($assignment->attachments && $assignment->attachments->count())

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Existing Attachments
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Files currently attached to this assignment.
                    </p>

                </div>


                <div class="space-y-3 p-6">

                    @foreach ($assignment->attachments as $attachment)

                        <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">

                            <div class="flex min-w-0 items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-slate-600 shadow-sm">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-5 w-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19.5 14.25v-8.625a2.25 2.25 0 0 0-2.25-2.25h-10.5a2.25 2.25 0 0 0-2.25 2.25v12.75a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-2.25m0 0 3-3m-3 3-3-3"
                                        />
                                    </svg>

                                </div>


                                <div class="min-w-0">

                                    <p class="truncate text-sm font-medium text-slate-800">
                                        {{ $attachment->original_name ?? $attachment->name ?? 'Attachment' }}
                                    </p>

                                    @if (isset($attachment->file_size))
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ number_format($attachment->file_size / 1024, 1) }} KB
                                        </p>
                                    @endif

                                </div>

                            </div>


                            @if (method_exists($attachment, 'getUrl') || isset($attachment->url))

                                <a
                                    href="{{ method_exists($attachment, 'getUrl') ? $attachment->getUrl() : $attachment->url }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="shrink-0 rounded-lg px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-white hover:text-slate-900"
                                >
                                    View
                                </a>

                            @endif

                        </div>

                    @endforeach

                </div>

            </section>

        @endif


        {{-- ===================================================== --}}
        {{-- ADD NEW ATTACHMENTS --}}
        {{-- ===================================================== --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Add Attachments
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Add additional worksheets, documents or reference files.
                </p>

            </div>


            <div class="p-6">

                <label
                    for="attachments"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-slate-400 hover:bg-slate-100"
                >

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-slate-600"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18.375 12.739 10.5 20.614a5.25 5.25 0 0 1-7.425-7.425l9.9-9.9a3.75 3.75 0 0 1 5.303 5.303l-9.9 9.9a2.25 2.25 0 0 1-3.182-3.182l8.132-8.132"
                            />
                        </svg>

                    </div>


                    <p class="mt-4 text-sm font-semibold text-slate-800">
                        Add more files
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        PDF, Word documents, Excel, PowerPoint, images or ZIP files
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        You can select multiple files.
                    </p>


                    <input
                        id="attachments"
                        name="attachments[]"
                        type="file"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.jpg,.jpeg,.png,.gif,.webp"
                        class="hidden"
                    >

                </label>


                <div
                    id="attachment-list"
                    class="mt-4 hidden"
                >

                    <div class="mb-2 flex items-center justify-between">

                        <p class="text-sm font-semibold text-slate-800">
                            New files
                        </p>

                        <button
                            type="button"
                            id="clear-attachments"
                            class="text-xs font-medium text-red-600 hover:text-red-700"
                        >
                            Clear all
                        </button>

                    </div>

                    <div
                        id="attachment-items"
                        class="space-y-2"
                    ></div>

                </div>

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- PUBLISHING --}}
        {{-- ===================================================== --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="p-6">

                <label class="flex cursor-pointer items-start gap-3">

                    <input
                        id="is_published"
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published', $assignment->is_published))
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300"
                    >

                    <span>

                        <span class="block text-sm font-medium text-slate-900">
                            Published
                        </span>

                        <span class="mt-1 block text-sm text-slate-500">
                            Students can see and submit this assignment.
                            Uncheck this to save it as a draft.
                        </span>

                    </span>

                </label>

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- ACTIONS --}}
        {{-- ===================================================== --}}

        <div class="flex items-center justify-between">

            <a
                href="{{ route('teacher.courses.assignments.show', [$course, $assignment]) }}"
                class="rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
            >
                Save Changes
            </button>

        </div>

    </form>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * =============================================================
     * LATE SUBMISSIONS
     * =============================================================
     */

    const lateCheckbox =
        document.getElementById('allow_late_submission');

    const lateSettings =
        document.getElementById('late-submission-settings');

    const lateDeadline =
        document.getElementById('late_submission_until');


    function updateLateSettings() {

        if (!lateCheckbox || !lateSettings) {
            return;
        }

        const enabled = lateCheckbox.checked;

        lateSettings.classList.toggle('hidden', !enabled);

        if (lateDeadline) {

            lateDeadline.disabled = !enabled;

            if (!enabled) {
                lateDeadline.value = '';
            }

        }
    }


    /*
     * =============================================================
     * RESUBMISSIONS
     * =============================================================
     */

    const resubmissionCheckbox =
        document.getElementById('allow_resubmission');

    const resubmissionSettings =
        document.getElementById('resubmission-settings');

    const maxAttempts =
        document.getElementById('max_attempts');


    function updateResubmissionSettings() {

        if (!resubmissionCheckbox || !resubmissionSettings) {
            return;
        }

        const enabled = resubmissionCheckbox.checked;

        resubmissionSettings.classList.toggle(
            'hidden',
            !enabled
        );

        if (maxAttempts) {

            maxAttempts.disabled = !enabled;

            if (!enabled) {
                maxAttempts.value = 1;
            }

        }
    }


    lateCheckbox?.addEventListener(
        'change',
        updateLateSettings
    );

    resubmissionCheckbox?.addEventListener(
        'change',
        updateResubmissionSettings
    );


    updateLateSettings();
    updateResubmissionSettings();


    /*
     * =============================================================
     * ATTACHMENT PREVIEW
     * =============================================================
     */

    const attachmentInput =
        document.getElementById('attachments');

    const attachmentList =
        document.getElementById('attachment-list');

    const attachmentItems =
        document.getElementById('attachment-items');

    const clearAttachments =
        document.getElementById('clear-attachments');


    function formatFileSize(bytes) {

        if (bytes < 1024) {
            return bytes + ' B';
        }

        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        if (bytes < 1024 * 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        return (bytes / (1024 * 1024 * 1024)).toFixed(1) + ' GB';
    }


    function escapeHtml(value) {

        const div = document.createElement('div');

        div.textContent = value;

        return div.innerHTML;
    }


    function renderAttachments() {

        if (!attachmentInput || !attachmentItems || !attachmentList) {
            return;
        }

        attachmentItems.innerHTML = '';

        const files =
            Array.from(attachmentInput.files || []);

        if (!files.length) {

            attachmentList.classList.add('hidden');

            return;
        }

        attachmentList.classList.remove('hidden');


        files.forEach(function (file) {

            const item =
                document.createElement('div');

            item.className =
                'flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4';


            item.innerHTML = `
                <div class="flex min-w-0 items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 14.25v-8.625a2.25 2.25 0 0 0-2.25-2.25h-10.5a2.25 2.25 0 0 0-2.25 2.25v12.75a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-2.25m0 0 3-3m-3 3-3-3"
                            />
                        </svg>

                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-sm font-medium text-slate-800">
                            ${escapeHtml(file.name)}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            ${formatFileSize(file.size)}
                        </p>

                    </div>

                </div>
            `;

            attachmentItems.appendChild(item);

        });
    }


    attachmentInput?.addEventListener(
        'change',
        renderAttachments
    );


    clearAttachments?.addEventListener(
        'click',
        function () {

            if (attachmentInput) {
                attachmentInput.value = '';
            }

            renderAttachments();

        }
    );


    /*
     * =============================================================
     * TIPTAP CONTENT
     * =============================================================
     */

    const form =
        document.getElementById('assignment-form');

    const instructionsField =
        document.getElementById('assignment-instructions');


    form?.addEventListener(
        'submit',
        function () {

            if (
                instructionsField &&
                window.assignmentEditor &&
                typeof window.assignmentEditor.getHTML === 'function'
            ) {

                instructionsField.value =
                    window.assignmentEditor.getHTML();

            }

        }
    );

});
</script>

@endpush


@push('styles')

<style>

.assignment-editor-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 34px;
    padding: 0 9px;
    border-radius: 8px;
    color: rgb(51 65 85);
    font-size: 13px;
    font-weight: 500;
    transition: background-color 150ms ease;
    cursor: pointer;
}

.assignment-editor-button:hover {
    background-color: rgb(226 232 240);
}


.assignment-tiptap-editor .ProseMirror {
    min-height: 380px;
    outline: none;
    font-size: 16px;
    line-height: 1.75;
    color: rgb(15 23 42);
    cursor: text;
}

.assignment-tiptap-editor .ProseMirror:focus {
    outline: none;
}


.assignment-tiptap-editor p {
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}


.assignment-tiptap-editor h1 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-size: 2rem;
    line-height: 1.2;
    font-weight: 700;
}

.assignment-tiptap-editor h2 {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
    line-height: 1.3;
    font-weight: 700;
}

.assignment-tiptap-editor h3 {
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
    line-height: 1.4;
    font-weight: 600;
}


.assignment-tiptap-editor ul {
    list-style-type: disc;
    margin: 1rem 0;
    padding-left: 1.75rem;
}

.assignment-tiptap-editor ol {
    list-style-type: decimal;
    margin: 1rem 0;
    padding-left: 1.75rem;
}


.assignment-tiptap-editor a {
    color: rgb(37 99 235);
    text-decoration: underline;
}


.assignment-tiptap-editor blockquote {
    margin: 1rem 0;
    border-left: 4px solid rgb(203 213 225);
    padding-left: 1rem;
    color: rgb(71 85 105);
}


.assignment-tiptap-editor img {
    display: block;
    max-width: 100%;
    height: auto;
    margin: 1.5rem auto;
    border-radius: 0.75rem;
}


.assignment-tiptap-editor table {
    width: 100%;
    margin: 1.5rem 0;
    border-collapse: collapse;
    table-layout: fixed;
}

.assignment-tiptap-editor td,
.assignment-tiptap-editor th {
    min-width: 1em;
    border: 1px solid rgb(203 213 225);
    padding: 8px;
    vertical-align: top;
}

.assignment-tiptap-editor th {
    background: rgb(241 245 249);
    font-weight: 600;
}

</style>

@endpush
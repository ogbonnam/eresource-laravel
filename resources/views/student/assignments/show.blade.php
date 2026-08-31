@extends('layouts.dashboard')

@section('title', $assignment->title)

@section('content')

<div class="mx-auto max-w-6xl space-y-8">

    {{-- ========================================================= --}}
    {{-- BACK --}}
    {{-- ========================================================= --}}

    <a
        href="{{ route('student.assignments.index') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
    >
        ← Back to Assignments
    </a>


    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="p-6 sm:p-8">

            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div>

                    <p class="text-sm font-semibold text-slate-500">

                        {{ $assignment->course->subject?->name ?? 'Course' }}

                        ·

                        {{ $assignment->course->name }}

                        @if ($assignment->course->schoolClass)
                            · {{ $assignment->course->schoolClass->name }}
                        @endif

                    </p>


                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                        {{ $assignment->title }}
                    </h1>


                    @if ($assignment->description)

                        <p class="mt-4 max-w-3xl text-base leading-7 text-slate-500">
                            {{ $assignment->description }}
                        </p>

                    @endif

                </div>


                {{-- Deadline --}}

                <div class="shrink-0">

                    @if ($assignment->due_at)

                        @if ($isPastDue)

                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">

                                <p class="text-xs font-semibold uppercase tracking-wide text-red-600">
                                    Past Due
                                </p>

                                <p class="mt-1 text-sm font-semibold text-red-800">
                                    {{ $assignment->due_at->format('d M Y, g:i A') }}
                                </p>

                                @if ($assignment->allow_late_submission)

                                    <p class="mt-1 text-xs text-red-700">
                                        Late submissions are allowed.
                                    </p>

                                @endif

                            </div>

                        @else

                            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">

                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Due
                                </p>

                                <p class="mt-1 text-sm font-semibold text-blue-800">
                                    {{ $assignment->due_at->format('d M Y, g:i A') }}
                                </p>

                            </div>

                        @endif

                    @else

                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Deadline
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-700">
                                No deadline
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Summary --}}

            <div class="mt-7 grid gap-3 sm:grid-cols-3">

                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Total Marks
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-900">
                        {{ $assignment->total_marks ?? 0 }}
                    </p>

                </div>


                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Teacher
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-900">
                        {{ $assignment->course->teacher?->name ?? 'Teacher' }}
                    </p>

                </div>


                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Attempts
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-900">

                        {{ $attempts->count() }}

                        @if ($assignment->max_attempts)

                            <span class="text-sm font-normal text-slate-500">
                                / {{ $assignment->max_attempts }}
                            </span>

                        @endif

                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- FLASH MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session('success'))

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">

            {{ session('success') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERRORS --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">

            <p class="text-sm font-semibold text-red-800">
                Please correct the following:
            </p>

            <ul class="mt-2 list-disc pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- PREVIOUS GRADED RESULT --}}
    {{-- ========================================================= --}}

    @if ($gradedSubmission)

        <section class="rounded-2xl border border-emerald-200 bg-white shadow-sm">

            <div class="border-b border-emerald-100 bg-emerald-50 px-6 py-5">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                            Teacher Result
                        </p>

                        <h2 class="mt-1 text-xl font-semibold text-emerald-900">
                            Attempt {{ $gradedSubmission->attempt_number }} Graded
                        </h2>

                    </div>


                    <div class="rounded-xl bg-white px-5 py-3 text-center shadow-sm">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Your Grade
                        </p>

                        <p class="mt-1 text-3xl font-bold text-emerald-700">

                            {{ $gradedSubmission->grade }}

                            <span class="text-base font-medium text-slate-500">
                                / {{ $assignment->total_marks }}
                            </span>

                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6 sm:p-8">

                {{-- Grading information --}}

                <div class="grid gap-4 sm:grid-cols-3">

                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Attempt
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-900">
                            Attempt {{ $gradedSubmission->attempt_number }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Submitted
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-900">

                            @if ($gradedSubmission->submitted_at)

                                {{ $gradedSubmission->submitted_at->format('d M Y, g:i A') }}

                            @else

                                —

                            @endif

                        </p>

                    </div>


                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Graded
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-900">

                            @if ($gradedSubmission->graded_at)

                                {{ $gradedSubmission->graded_at->format('d M Y, g:i A') }}

                            @else

                                Recently graded

                            @endif

                        </p>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- TEACHER FEEDBACK --}}
                {{-- ================================================= --}}

                @if (
                    $gradedSubmission->feedback !== null &&
                    trim((string) $gradedSubmission->feedback) !== ''
                )

                    <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50">

                        <div class="border-b border-blue-100 px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-5 w-5 text-blue-600"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.056v7.182a2.25 2.25 0 0 1-2.25 2.25h-9.69a2.25 2.25 0 0 1-1.591-.659l-3.182-3.182A2.25 2.25 0 0 1 4.5 14.568V6.75A2.25 2.25 0 0 1 6.75 4.5h9.69a2.25 2.25 0 0 1 1.591.659l1.591 1.591"
                                        />
                                    </svg>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                        Teacher Feedback
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-blue-900">
                                        Feedback on Attempt {{ $gradedSubmission->attempt_number }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="assignment-content px-5 py-5 text-blue-950">

                            {!! $gradedSubmission->feedback !!}

                        </div>

                    </div>

                @else

                    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5">

                        <p class="text-sm font-semibold text-slate-700">
                            Your teacher has not provided written feedback for this attempt.
                        </p>

                    </div>

                @endif


                {{-- Graded submitted answer --}}

                @if ($gradedSubmission->content)

                    <div class="mt-6">

                        <h3 class="text-sm font-semibold text-slate-900">
                            Submitted Answer
                        </h3>

                        <div class="assignment-content mt-3 rounded-xl border border-slate-200 bg-slate-50 p-5">

                            {!! $gradedSubmission->content !!}

                        </div>

                    </div>

                @endif


                {{-- Graded files --}}

                @if ($gradedSubmission->files->count())

                    <div class="mt-6">

                        <h3 class="text-sm font-semibold text-slate-900">
                            Submitted Files
                        </h3>

                        <div class="mt-3 space-y-2">

                            @foreach ($gradedSubmission->files as $file)

                                <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-4">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-slate-600"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A2.625 2.625 0 0 1 12 5.625v-1.5A3.375 3.375 0 0 0 8.625.75H5.25A2.25 2.25 0 0 0 3 3v18a2.25 2.25 0 0 0 2.25 2.25h12A2.25 2.25 0 0 0 19.5 21V14.25Z"
                                            />
                                        </svg>

                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-slate-900">
                                            {{ $file->original_name }}
                                        </p>

                                        @if ($file->file_size)

                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </section>

    @endif


    {{-- ========================================================= --}}
    {{-- MAIN GRID --}}
    {{-- ========================================================= --}}

    <div class="grid gap-6 lg:grid-cols-3">


        {{-- ===================================================== --}}
        {{-- MAIN COLUMN --}}
        {{-- ===================================================== --}}

        <div class="space-y-6 lg:col-span-2">


            {{-- ================================================= --}}
            {{-- INSTRUCTIONS --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Assignment Instructions
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Read the instructions carefully before submitting your work.
                    </p>

                </div>


                <div class="assignment-content px-6 py-7 sm:px-8">

                    @if ($assignment->instructions)

                        {!! $assignment->instructions !!}

                    @else

                        <p class="text-sm text-slate-500">
                            No additional instructions were provided.
                        </p>

                    @endif

                </div>

            </section>


            {{-- ================================================= --}}
            {{-- TEACHER ATTACHMENTS --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Teacher Attachments
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Files provided by your teacher for this assignment.
                    </p>

                </div>


                <div class="p-6">

                    @if ($assignment->attachments->count())

                        <div class="space-y-3">

                            @foreach ($assignment->attachments as $attachment)

                                <a
                                    href="{{ route(
                                        'student.assignments.attachments.download',
                                        [
                                            'assignment' => $assignment->id,
                                            'attachment' => $attachment->id,
                                        ]
                                    ) }}"
                                    target="_blank"
                                    class="group flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-slate-300 hover:bg-slate-50"
                                >

                                    <div class="flex min-w-0 items-center gap-3">

                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="h-5 w-5 text-slate-600"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A2.625 2.625 0 0 1 12 5.625v-1.5A3.375 3.375 0 0 0 8.625.75H5.25A2.25 2.25 0 0 0 3 3v18a2.25 2.25 0 0 0 2.25 2.25h12A2.25 2.25 0 0 0 19.5 21V14.25Z"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 11.25v6m0 0 2.25-2.25M12 17.25l-2.25-2.25"
                                                />
                                            </svg>

                                        </div>


                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-900">
                                                {{ $attachment->original_name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">

                                                @if ($attachment->file_size)

                                                    {{ number_format($attachment->file_size / 1024 / 1024, 2) }} MB

                                                @endif

                                                @if ($attachment->mime_type)

                                                    · {{ $attachment->mime_type }}

                                                @endif

                                            </p>

                                        </div>

                                    </div>


                                    <span class="shrink-0 text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                                        Open
                                    </span>

                                </a>

                            @endforeach

                        </div>

                    @else

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                            <p class="text-sm font-medium text-slate-700">
                                No attachments
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Your teacher has not attached any files.
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- ================================================= --}}
            {{-- CURRENT ATTEMPT --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Your Work
                            </h2>


                            @if ($submission)

                                <p class="mt-1 text-sm text-slate-500">
                                    Current Attempt {{ $submission->attempt_number }}
                                </p>

                            @else

                                <p class="mt-1 text-sm text-slate-500">
                                    You have not started this assignment yet.
                                </p>

                            @endif

                        </div>


                        @if ($submission)

                            @if ($submission->status === 'draft')

                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Draft
                                </span>

                            @elseif ($submission->status === 'submitted')

                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    Submitted
                                </span>

                            @elseif ($submission->status === 'graded')

                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Graded
                                </span>

                            @endif

                        @endif

                    </div>

                </div>


                <div class="p-6">


                    {{-- ================================================= --}}
                    {{-- NO ATTEMPT --}}
                    {{-- ================================================= --}}

                    @if (!$submission)

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">

                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-6 w-6 text-slate-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6v12m6-6H6"
                                    />
                                </svg>

                            </div>


                            <h3 class="mt-4 text-sm font-semibold text-slate-900">
                                Start your assignment
                            </h3>


                            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                Start your first attempt when you are ready.
                            </p>


                            <a
                                href="{{ route(
                                    'student.assignments.show',
                                    $assignment
                                ) }}?new_attempt=1"
                                class="mt-6 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                            >
                                Start Attempt 1
                            </a>

                        </div>


                    {{-- ================================================= --}}
                    {{-- DRAFT --}}
                    {{-- ================================================= --}}

                    @elseif ($submission->status === 'draft')

                        <form
                            method="POST"
                            action="{{ route(
                                'student.assignments.submit',
                                $assignment
                            ) }}"
                            id="assignment-form"
                            enctype="multipart/form-data"
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="attempt_id"
                                value="{{ $submission->id }}"
                            >


                            <input
                                type="hidden"
                                name="content"
                                id="assignment-content"
                                value="{{ old(
                                    'content',
                                    $submission->content
                                ) }}"
                            >


                            {{-- Editor --}}

                            <div class="overflow-hidden rounded-xl border border-slate-200">

                                <div class="flex flex-wrap gap-1 border-b border-slate-200 bg-slate-50 p-2">

                                    <button
                                        type="button"
                                        data-assignment-command="bold"
                                        class="rounded-lg px-3 py-2 text-sm font-semibold hover:bg-white"
                                    >
                                        B
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="italic"
                                        class="rounded-lg px-3 py-2 text-sm italic hover:bg-white"
                                    >
                                        I
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="underline"
                                        class="rounded-lg px-3 py-2 text-sm underline hover:bg-white"
                                    >
                                        U
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="bulletList"
                                        class="rounded-lg px-3 py-2 text-sm hover:bg-white"
                                    >
                                        • List
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="orderedList"
                                        class="rounded-lg px-3 py-2 text-sm hover:bg-white"
                                    >
                                        1. List
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="heading2"
                                        class="rounded-lg px-3 py-2 text-sm hover:bg-white"
                                    >
                                        H2
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="undo"
                                        class="rounded-lg px-3 py-2 text-sm hover:bg-white"
                                    >
                                        Undo
                                    </button>

                                    <button
                                        type="button"
                                        data-assignment-command="redo"
                                        class="rounded-lg px-3 py-2 text-sm hover:bg-white"
                                    >
                                        Redo
                                    </button>

                                </div>


                                <div
                                    id="assignment-editor"
                                    class="min-h-[300px] bg-white px-5 py-4 outline-none"
                                ></div>

                            </div>


                            {{-- Existing files --}}

                            @if ($submission->files->count())

                                <div class="mt-6">

                                    <p class="text-sm font-semibold text-slate-900">
                                        Attached Files
                                    </p>


                                    <div class="mt-3 space-y-2">

                                        @foreach ($submission->files as $file)

                                            <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 p-3">

                                                <div class="min-w-0">

                                                    <p class="truncate text-sm font-medium text-slate-800">
                                                        {{ $file->original_name }}
                                                    </p>

                                                    @if ($file->file_size)

                                                        <p class="mt-1 text-xs text-slate-500">
                                                            {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                                                        </p>

                                                    @endif

                                                </div>


                                                <button
                                                    type="submit"
                                                    formmethod="POST"
                                                    formaction="{{ route(
                                                        'student.assignments.files.remove',
                                                        $file
                                                    ) }}"
                                                    class="shrink-0 text-sm font-semibold text-red-600 hover:text-red-700"
                                                    onclick="return confirm('Remove this file?')"
                                                >
                                                    Remove
                                                </button>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif


                            {{-- New files --}}

                            <div class="mt-6">

                                <label
                                    for="assignment-files"
                                    class="block text-sm font-semibold text-slate-900"
                                >
                                    Attach your work
                                </label>

                                <p class="mt-1 text-sm text-slate-500">
                                    PDF, Word documents, or multiple documents.
                                    Maximum 20MB per file.
                                </p>


                                <input
                                    id="assignment-files"
                                    name="files[]"
                                    type="file"
                                    multiple
                                    accept=".pdf,.doc,.docx"
                                    class="mt-3 block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold"
                                >

                            </div>


                            {{-- Actions --}}

                            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">

                                <button
                                    type="submit"
                                    formaction="{{ route(
                                        'student.assignments.draft',
                                        $assignment
                                    ) }}"
                                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Save Draft
                                </button>


                                @if ($canSubmitCurrentAttempt)

                                    <button
                                        type="submit"
                                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                                    >
                                        Submit Attempt {{ $submission->attempt_number }}
                                    </button>

                                @else

                                    <button
                                        type="button"
                                        disabled
                                        class="cursor-not-allowed rounded-xl bg-slate-300 px-5 py-3 text-sm font-semibold text-white"
                                    >
                                        Submission Closed
                                    </button>

                                @endif

                            </div>

                        </form>


                    {{-- ================================================= --}}
                    {{-- SUBMITTED --}}
                    {{-- ================================================= --}}

                    @elseif ($submission->status === 'submitted')

                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">

                            <p class="text-sm font-semibold text-blue-800">
                                Attempt {{ $submission->attempt_number }} has been submitted.
                            </p>

                            <p class="mt-1 text-sm text-blue-700">
                                Your teacher has not graded this attempt yet.
                            </p>

                        </div>


                        @if ($submission->content)

                            <div class="mt-6">

                                <h3 class="text-sm font-semibold text-slate-900">
                                    Submitted Answer
                                </h3>

                                <div class="assignment-content mt-3 rounded-xl border border-slate-200 bg-slate-50 p-5">
                                    {!! $submission->content !!}
                                </div>

                            </div>

                        @endif


                    {{-- ================================================= --}}
                    {{-- GRADED CURRENT ATTEMPT --}}
                    {{-- ================================================= --}}

                    @elseif ($submission->status === 'graded')

                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">

                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                                Your Grade
                            </p>

                            <p class="mt-1 text-3xl font-bold text-emerald-800">

                                {{ $submission->grade }}

                                <span class="text-base font-medium text-emerald-600">
                                    / {{ $assignment->total_marks }}
                                </span>

                            </p>

                        </div>

                    @endif

                </div>

            </section>

        </div>


        {{-- ===================================================== --}}
        {{-- RIGHT SIDEBAR --}}
        {{-- ===================================================== --}}

        <aside class="space-y-6">


            {{-- ================================================= --}}
            {{-- ATTEMPT ACTION --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h2 class="font-semibold text-slate-900">
                    Attempts
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Your submission history.
                </p>


                @if ($canStartNewAttempt)

                    <a
                        href="{{ route(
                            'student.assignments.show',
                            $assignment
                        ) }}?new_attempt=1"
                        class="mt-5 flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Start Attempt {{ $attempts->count() + 1 }}
                    </a>

                @elseif (
                    $assignment->allow_resubmission &&
                    $assignment->max_attempts &&
                    $attempts->count() >= $assignment->max_attempts
                )

                    <div class="mt-5 rounded-xl bg-slate-50 p-4 text-center">

                        <p class="text-sm font-semibold text-slate-700">
                            Maximum attempts reached
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            You have used all {{ $assignment->max_attempts }} allowed attempts.
                        </p>

                    </div>

                @elseif (!$assignment->allow_resubmission)

                    <div class="mt-5 rounded-xl bg-slate-50 p-4 text-center">

                        <p class="text-sm font-semibold text-slate-700">
                            One attempt only
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Resubmissions are not allowed for this assignment.
                        </p>

                    </div>

                @endif

            </section>


            {{-- ================================================= --}}
            {{-- ATTEMPT HISTORY --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Attempt History
                    </h2>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse ($attempts as $attempt)

                        <div class="p-5">

                            <div class="flex items-start justify-between gap-3">

                                <div>

                                    <p class="text-sm font-semibold text-slate-900">
                                        Attempt {{ $attempt->attempt_number }}
                                    </p>


                                    @if ($attempt->submitted_at)

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $attempt->submitted_at->format('d M Y, g:i A') }}
                                        </p>

                                    @elseif ($attempt->status === 'draft')

                                        <p class="mt-1 text-xs text-amber-600">
                                            Draft
                                        </p>

                                    @endif

                                </div>


                                @if ($attempt->status === 'draft')

                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        Draft
                                    </span>

                                @elseif ($attempt->status === 'submitted')

                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                        Submitted
                                    </span>

                                @elseif ($attempt->status === 'graded')

                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        Graded
                                    </span>

                                @endif

                            </div>


                            @if ($attempt->is_late)

                                <div class="mt-3">

                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                        Late
                                    </span>

                                </div>

                            @endif


                            @if ($attempt->status === 'graded')

                                <p class="mt-3 text-sm font-semibold text-slate-900">

                                    {{ $attempt->grade }}

                                    <span class="font-normal text-slate-500">
                                        / {{ $assignment->total_marks }}
                                    </span>

                                </p>


                                @if (
                                    $attempt->feedback !== null &&
                                    trim((string) $attempt->feedback) !== ''
                                )

                                    <div class="mt-3 flex items-center gap-2 text-xs font-semibold text-blue-600">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-4 w-4"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.056v7.182a2.25 2.25 0 0 1-2.25 2.25h-9.69a2.25 2.25 0 0 1-1.591-.659l-3.182-3.182A2.25 2.25 0 0 1 6 18.75h9.69a2.25 2.25 0 0 0 2.25-2.25V10.5"
                                            />
                                        </svg>

                                        Feedback available

                                    </div>

                                @endif

                            @endif


                            @if (
                                $submission &&
                                $attempt->id === $submission->id
                            )

                                <p class="mt-3 text-xs font-semibold text-slate-400">
                                    Current attempt
                                </p>

                            @endif

                        </div>

                    @empty

                        <div class="p-6 text-center">

                            <p class="text-sm text-slate-500">
                                No attempts yet.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>


            {{-- ================================================= --}}
            {{-- SUBMISSION RULES --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h2 class="font-semibold text-slate-900">
                    Submission Rules
                </h2>


                <div class="mt-4 space-y-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Maximum Attempts
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ $assignment->max_attempts ?: 'Unlimited' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Resubmissions
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ $assignment->allow_resubmission ? 'Allowed' : 'Not allowed' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Late Submission
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ $assignment->allow_late_submission ? 'Allowed' : 'Not allowed' }}
                        </p>

                    </div>


                    @if (
                        $assignment->allow_late_submission &&
                        $assignment->late_submission_until
                    )

                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Final Late Deadline
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-900">
                                {{ $assignment->late_submission_until->format('d M Y, g:i A') }}
                            </p>

                        </div>

                    @endif

                </div>

            </section>

        </aside>

    </div>

</div>

@endsection


@push('styles')

<style>

    .assignment-content {
        color: rgb(51 65 85);
        font-size: 16px;
        line-height: 1.8;
    }

    .assignment-content p {
        margin-top: 0.9rem;
        margin-bottom: 0.9rem;
    }

    .assignment-content h1 {
        margin-top: 1.75rem;
        margin-bottom: 1rem;
        font-size: 2rem;
        line-height: 1.25;
        font-weight: 700;
        color: rgb(15 23 42);
    }

    .assignment-content h2 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
        line-height: 1.3;
        font-weight: 700;
        color: rgb(15 23 42);
    }

    .assignment-content h3 {
        margin-top: 1.25rem;
        margin-bottom: 0.6rem;
        font-size: 1.25rem;
        line-height: 1.4;
        font-weight: 600;
        color: rgb(15 23 42);
    }

    .assignment-content ul {
        margin: 1rem 0;
        padding-left: 1.75rem;
        list-style-type: disc;
    }

    .assignment-content ol {
        margin: 1rem 0;
        padding-left: 1.75rem;
        list-style-type: decimal;
    }

    .assignment-content li {
        margin-top: 0.35rem;
        margin-bottom: 0.35rem;
    }

    .assignment-content a {
        color: rgb(37 99 235);
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .assignment-content blockquote {
        margin: 1.25rem 0;
        border-left: 4px solid rgb(203 213 225);
        padding-left: 1rem;
        color: rgb(71 85 105);
    }

    .assignment-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem auto;
        border-radius: 0.75rem;
    }

    .assignment-content table {
        width: 100%;
        margin: 1.5rem 0;
        border-collapse: collapse;
    }

    .assignment-content th,
    .assignment-content td {
        border: 1px solid rgb(226 232 240);
        padding: 0.75rem;
        text-align: left;
    }

    .assignment-content th {
        background: rgb(248 250 252);
        font-weight: 600;
        color: rgb(15 23 42);
    }

    #assignment-editor {
        min-height: 300px;
    }

    #assignment-editor .ProseMirror {
        min-height: 300px;
        outline: none;
    }

    #assignment-editor .ProseMirror p {
        margin: 0.75rem 0;
    }

    #assignment-editor .ProseMirror h1 {
        margin: 1.5rem 0 0.75rem;
        font-size: 2rem;
        font-weight: 700;
    }

    #assignment-editor .ProseMirror h2 {
        margin: 1.25rem 0 0.75rem;
        font-size: 1.5rem;
        font-weight: 700;
    }

    #assignment-editor .ProseMirror h3 {
        margin: 1rem 0 0.5rem;
        font-size: 1.25rem;
        font-weight: 600;
    }

    #assignment-editor .ProseMirror ul {
        list-style-type: disc;
        padding-left: 1.75rem;
    }

    #assignment-editor .ProseMirror ol {
        list-style-type: decimal;
        padding-left: 1.75rem;
    }

    #assignment-editor .ProseMirror blockquote {
        margin: 1rem 0;
        border-left: 4px solid rgb(203 213 225);
        padding-left: 1rem;
    }

</style>

@endpush
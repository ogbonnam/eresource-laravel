@extends('layouts.dashboard')

@section('title', 'Review Submission · ' . $assignment->title)

@section('content')

<div class="mx-auto max-w-7xl space-y-6">

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

        <div class="mt-5 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

            <div>

                <div class="flex flex-wrap items-center gap-2">

                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        Attempt {{ $submission->attempt_number ?? 1 }}
                    </span>

                    @if ($submission->is_late)

                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                            Late
                        </span>

                    @endif

                    @if (($submission->status ?? '') === 'graded')

                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                            Graded
                        </span>

                    @else

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            Awaiting Grade
                        </span>

                    @endif

                </div>

                <p class="mt-4 text-sm font-medium text-slate-500">
                    {{ $assignment->course->subject?->name ?? 'Course' }}

                    @if ($assignment->course->schoolClass)
                        · {{ $assignment->course->schoolClass->name }}
                    @endif
                </p>

                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                    {{ $assignment->title }}
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Reviewing submission from
                    <span class="font-semibold text-slate-700">
                        {{ $submission->student?->name ?? 'Unknown Student' }}
                    </span>
                </p>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Assignment
                </p>

                <p class="mt-1 text-lg font-semibold text-slate-900">
                    {{ $assignment->total_marks ?? 0 }} marks
                </p>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session('success'))

        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 p-4">

            <p class="text-sm font-semibold text-red-800">
                Please correct the following:
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- STUDENT INFORMATION --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <h2 class="font-semibold text-slate-900">
                Student
            </h2>

        </div>

        <div class="p-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-slate-100 text-lg font-semibold text-slate-600">

                    {{ strtoupper(substr($submission->student?->name ?? 'S', 0, 1)) }}

                </div>

                <div>

                    <h3 class="text-lg font-semibold text-slate-900">
                        {{ $submission->student?->name ?? 'Unknown Student' }}
                    </h3>

                    @if ($submission->student?->email)

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $submission->student->email }}
                        </p>

                    @endif

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- MAIN GRID --}}
    {{-- ========================================================= --}}

    <div class="grid gap-6 lg:grid-cols-3">


        {{-- ===================================================== --}}
        {{-- STUDENT SUBMISSION --}}
        {{-- ===================================================== --}}

        <div class="space-y-6 lg:col-span-2">


            {{-- ================================================= --}}
            {{-- SUBMISSION DETAILS --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Student Submission
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Attempt {{ $submission->attempt_number ?? 1 }}
                            </p>

                        </div>

                        <div class="text-sm text-slate-500">

                            @if ($submission->submitted_at)

                                Submitted
                                {{ $submission->submitted_at->format('M d, Y · g:i A') }}

                            @else

                                Submitted
                                {{ $submission->created_at?->format('M d, Y · g:i A') }}

                            @endif

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    @php
                        $submissionText =
                            $submission->content
                            ?? $submission->answer
                            ?? $submission->submission_text
                            ?? null;
                    @endphp

                    @if ($submissionText)

                        <div class="prose max-w-none text-slate-700">

                            {!! nl2br(e($submissionText)) !!}

                        </div>

                    @else

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                            <p class="text-sm font-medium text-slate-700">
                                No written response
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                This submission may contain uploaded files instead.
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- ================================================= --}}
            {{-- STUDENT FILES --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Submitted Files
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Files submitted by the student for this attempt.
                        </p>

                    </div>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $submission->files->count() }}
                    </span>

                </div>


                <div class="p-6">

                    @if ($submission->files->isNotEmpty())

                        <div class="space-y-3">

                            @foreach ($submission->files as $file)

                                @php
                                    $extension = strtolower(
                                        pathinfo(
                                            $file->original_name ?? $file->file_path,
                                            PATHINFO_EXTENSION
                                        )
                                    );
                                @endphp

                                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm">

                                        @if ($extension === 'pdf')

                                            <span class="text-xs font-bold text-red-600">
                                                PDF
                                            </span>

                                        @elseif (in_array($extension, ['doc', 'docx']))

                                            <span class="text-xs font-bold text-blue-600">
                                                DOC
                                            </span>

                                        @elseif (in_array($extension, ['xls', 'xlsx']))

                                            <span class="text-xs font-bold text-green-600">
                                                XLS
                                            </span>

                                        @elseif (in_array($extension, ['ppt', 'pptx']))

                                            <span class="text-xs font-bold text-orange-600">
                                                PPT
                                            </span>

                                        @elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))

                                            <span class="text-xs font-bold text-purple-600">
                                                IMG
                                            </span>

                                        @else

                                            <span class="text-xs font-bold text-slate-500">
                                                FILE
                                            </span>

                                        @endif

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="truncate text-sm font-semibold text-slate-800"
                                            title="{{ $file->original_name }}"
                                        >
                                            {{ $file->original_name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">

                                            @if ($file->file_size)

                                                {{ number_format($file->file_size / 1024 / 1024, 2) }} MB

                                            @endif

                                            @if ($file->mime_type)

                                                @if ($file->file_size)
                                                    ·
                                                @endif

                                                {{ $file->mime_type }}

                                            @endif

                                        </p>

                                    </div>


                                    <a
                                        href="{{ route(
                                            'teacher.courses.assignments.submissions.files.download',
                                            [
                                                'course' => $course,
                                                'assignment' => $assignment,
                                                'file' => $file,
                                            ]
                                        ) }}"
                                        target="_blank"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                    >
                                        Download
                                    </a>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                            <p class="text-sm font-medium text-slate-700">
                                No files submitted
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                This attempt does not contain any uploaded files.
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- ================================================= --}}
            {{-- ATTEMPT HISTORY --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Attempt History
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        All attempts submitted by this student.
                    </p>

                </div>


                <div class="divide-y divide-slate-100">

                    @foreach ($attempts as $attempt)

                        <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-start gap-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                    {{ $attempt->attempt_number ?? 1 }}
                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-slate-900">

                                        Attempt {{ $attempt->attempt_number ?? 1 }}

                                        @if ($attempt->id === $submission->id)

                                            <span class="ml-2 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">
                                                Current
                                            </span>

                                        @endif

                                    </p>


                                    <p class="mt-1 text-xs text-slate-500">

                                        @if ($attempt->submitted_at)

                                            {{ $attempt->submitted_at->format('M d, Y · g:i A') }}

                                        @else

                                            {{ $attempt->created_at?->format('M d, Y · g:i A') }}

                                        @endif

                                    </p>

                                </div>

                            </div>


                            <div class="flex items-center gap-3">

                                @if (($attempt->status ?? '') === 'graded')

                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        Graded
                                    </span>

                                    @if ($attempt->grade !== null)

                                        <span class="text-sm font-semibold text-slate-900">
                                            {{ $attempt->grade }}/{{ $assignment->total_marks }}
                                        </span>

                                    @endif

                                @else

                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Not graded
                                    </span>

                                @endif


                                @if ($attempt->id !== $submission->id)

                                    <a
                                        href="{{ route(
                                            'teacher.courses.assignments.submissions.review',
                                            [
                                                'course' => $course,
                                                'assignment' => $assignment,
                                                'submission' => $attempt,
                                            ]
                                        ) }}"
                                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Review
                                    </a>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>

        </div>


        {{-- ===================================================== --}}
        {{-- GRADING SIDEBAR --}}
        {{-- ===================================================== --}}

        <aside class="space-y-6">


            {{-- ================================================= --}}
            {{-- GRADING --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Grade Submission
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Grade this attempt and provide feedback.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route(
                        'teacher.courses.assignments.submissions.grade',
                        [
                            'course' => $course,
                            'assignment' => $assignment,
                            'submission' => $submission,
                        ]
                    ) }}"
                    class="space-y-5 p-6"
                >

                    @csrf

                    @method('PUT')


                    {{-- Grade --}}

                    <div>

                        <label
                            for="grade"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Grade
                        </label>

                        <div class="mt-2 flex items-center gap-2">

                            <input
                                id="grade"
                                name="grade"
                                type="number"
                                min="0"
                                max="{{ $assignment->total_marks }}"
                                step="0.01"
                                value="{{ old('grade', $submission->grade) }}"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                            >

                            <span class="shrink-0 text-sm font-medium text-slate-500">
                                / {{ $assignment->total_marks }}
                            </span>

                        </div>

                    </div>


                    {{-- Feedback --}}

                    <div>

                        <label
                            for="feedback"
                            class="block text-sm font-semibold text-slate-700"
                        >
                            Feedback
                        </label>

                        <textarea
                            id="feedback"
                            name="feedback"
                            rows="7"
                            placeholder="Write feedback for the student..."
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >{{ old('feedback', $submission->feedback) }}</textarea>

                    </div>


                    {{-- Submit --}}

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        {{ $submission->grade !== null ? 'Update Grade' : 'Save Grade' }}
                    </button>

                </form>

            </section>


            {{-- ================================================= --}}
            {{-- CURRENT GRADE --}}
            {{-- ================================================= --}}

            @if ($submission->grade !== null)

                <section class="rounded-2xl border border-green-200 bg-green-50 p-6">

                    <p class="text-xs font-semibold uppercase tracking-wide text-green-700">
                        Current Grade
                    </p>

                    <div class="mt-2 flex items-end gap-2">

                        <span class="text-4xl font-bold text-green-800">
                            {{ $submission->grade }}
                        </span>

                        <span class="pb-1 text-sm font-medium text-green-700">
                            / {{ $assignment->total_marks }}
                        </span>

                    </div>

                    @if ($submission->feedback)

                        <div class="mt-4 border-t border-green-200 pt-4">

                            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">
                                Feedback
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-green-800">
                                {{ $submission->feedback }}
                            </p>

                        </div>

                    @endif

                </section>

            @endif


            {{-- ================================================= --}}
            {{-- ASSIGNMENT INFORMATION --}}
            {{-- ================================================= --}}

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Assignment
                    </h2>

                </div>

                <div class="divide-y divide-slate-100">

                    <div class="px-6 py-4">

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Total Marks
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $assignment->total_marks }}
                        </p>

                    </div>


                    <div class="px-6 py-4">

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Due Date
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-900">

                            @if ($assignment->due_at)

                                {{ $assignment->due_at->format('M d, Y · g:i A') }}

                            @else

                                No due date

                            @endif

                        </p>

                    </div>


                    <div class="px-6 py-4">

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Attempt
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $submission->attempt_number ?? 1 }}
                        </p>

                    </div>

                </div>

            </section>

        </aside>

    </div>


    {{-- ========================================================= --}}
    {{-- BOTTOM --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between border-t border-slate-200 pt-6">

        <a
            href="{{ route('teacher.courses.assignments.show', [$course, $assignment]) }}"
            class="rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
        >
            ← Back to Assignment
        </a>

        <a
            href="{{ route('teacher.courses.assignments.index', $course) }}"
            class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
        >
            All Assignments
        </a>

    </div>

</div>

@endsection
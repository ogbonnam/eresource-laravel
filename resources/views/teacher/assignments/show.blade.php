@extends('layouts.dashboard')

@section('title', $assignment->title . ' · ' . $course->name)

@section('content')

<div class="mx-auto max-w-6xl space-y-8">

```
{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<section>

    <a
        href="{{ route('teacher.courses.assignments.index', $course) }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
    >
        ← Back to Assignments
    </a>

    <div class="mt-6 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

        <div>

            <div class="flex flex-wrap items-center gap-2">

                @if ($assignment->is_published)

                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                        Published
                    </span>

                @else

                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        Draft
                    </span>

                @endif


                @if ($assignment->due_at)

                    @if ($assignment->due_at->isPast())

                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            Past Due
                        </span>

                    @else

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            Due {{ $assignment->due_at->format('M d, Y · g:i A') }}
                        </span>

                    @endif

                @endif

            </div>


            <p class="mt-4 text-sm font-medium text-slate-500">

                {{ $course->subject?->name ?? 'Course' }}

                @if ($course->schoolClass)
                    · {{ $course->schoolClass->name }}
                @endif

            </p>


            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                {{ $assignment->title }}
            </h1>


            @if ($assignment->description)

                <p class="mt-3 max-w-3xl text-slate-500">
                    {{ $assignment->description }}
                </p>

            @endif

        </div>


        <div class="flex flex-wrap items-center gap-3">

            <a
                href="{{ route('teacher.courses.assignments.edit', [$course, $assignment]) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Edit Assignment
            </a>

        </div>

    </div>

</section>


{{-- ========================================================= --}}
{{-- ASSIGNMENT SUMMARY --}}
{{-- ========================================================= --}}

<section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

    {{-- Total Marks --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Total Marks
        </p>

        <p class="mt-2 text-2xl font-semibold text-slate-900">
            {{ $assignment->total_marks ?? 0 }}
        </p>

    </div>


    {{-- Due Date --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Due Date
        </p>

        @if ($assignment->due_at)

            <p class="mt-2 text-lg font-semibold text-slate-900">
                {{ $assignment->due_at->format('M d, Y') }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                {{ $assignment->due_at->format('g:i A') }}
            </p>

        @else

            <p class="mt-2 text-lg font-semibold text-slate-400">
                No due date
            </p>

        @endif

    </div>


    {{-- Students --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Students
        </p>

        <p class="mt-2 text-2xl font-semibold text-slate-900">
            {{ $course->students_count ?? $course->students()->count() }}
        </p>

        <p class="mt-1 text-sm text-slate-500">
            Enrolled
        </p>

    </div>


    {{-- Submissions --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Submissions
        </p>

        <p class="mt-2 text-2xl font-semibold text-slate-900">
            {{ $assignment->submissions->count() }}
        </p>

        <p class="mt-1 text-sm text-slate-500">
            Submitted
        </p>

    </div>

</section>


{{-- ========================================================= --}}
{{-- MAIN CONTENT --}}
{{-- ========================================================= --}}

<div class="grid gap-6 lg:grid-cols-3">


    {{-- ===================================================== --}}
    {{-- INSTRUCTIONS --}}
    {{-- ===================================================== --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

        <div class="border-b border-slate-100 px-6 py-5">

            <h2 class="font-semibold text-slate-900">
                Assignment Instructions
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Instructions provided to students.
            </p>

        </div>


        <div class="p-6">

            @if ($assignment->instructions)

                <div class="assignment-content">
                    {!! $assignment->instructions !!}
                </div>

            @else

                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                    <p class="text-sm font-medium text-slate-700">
                        No instructions have been added.
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Add instructions so students know what they need to complete.
                    </p>

                </div>

            @endif

        </div>

    </section>


    {{-- ===================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ===================================================== --}}

    <aside class="space-y-6">


        {{-- ================================================= --}}
        {{-- ASSIGNMENT DETAILS --}}
        {{-- ================================================= --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Assignment Details
                </h2>

            </div>


            <div class="divide-y divide-slate-100">

                <div class="px-6 py-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">

                        @if ($assignment->is_published)
                            Published
                        @else
                            Draft
                        @endif

                    </p>

                </div>


                <div class="px-6 py-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Marks
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $assignment->total_marks ?? 0 }} marks
                    </p>

                </div>


                <div class="px-6 py-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Due
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">

                        @if ($assignment->due_at)

                            {{ $assignment->due_at->format('M d, Y') }}

                            <span class="font-normal text-slate-500">
                                at {{ $assignment->due_at->format('g:i A') }}
                            </span>

                        @else

                            No due date

                        @endif

                    </p>

                </div>


                <div class="px-6 py-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Created
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $assignment->created_at?->format('M d, Y') }}
                    </p>

                </div>

            </div>

        </section>


        {{-- ================================================= --}}
        {{-- ATTACHMENTS --}}
        {{-- ================================================= --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Attachments
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Files attached to this assignment.
                    </p>

                </div>

                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $assignment->attachments->count() }}
                </span>

            </div>


            <div class="p-6">

                @if ($assignment->attachments->isNotEmpty())

                    <div class="space-y-3">

                        @foreach ($assignment->attachments as $attachment)

                            @php
                                $extension = strtolower(
                                    pathinfo($attachment->original_name, PATHINFO_EXTENSION)
                                );

                                $fileUrl = Storage::disk('public')->url(
                                    $attachment->file_path
                                );
                            @endphp


                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">

                                {{-- File Icon --}}

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">

                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-purple-600"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.409 2.409M3.75 19.5h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008H13.5V8.25Z"
                                            />
                                        </svg>

                                    @elseif ($extension === 'pdf')

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-red-600"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A2.625 2.625 0 0 1 12 5.625v-1.5A3.375 3.375 0 0 0 8.625.75H6.75A3.375 3.375 0 0 0 3.375 4.125v15.75A3.375 3.375 0 0 0 6.75 23.25h9.375A3.375 3.375 0 0 0 19.5 19.875v-5.625Z"
                                            />
                                        </svg>

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

                                    @elseif ($extension === 'zip')

                                        <span class="text-xs font-bold text-amber-600">
                                            ZIP
                                        </span>

                                    @else

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-slate-500"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A2.625 2.625 0 0 1 12 5.625v-1.5A3.375 3.375 0 0 0 8.625.75H6.75A3.375 3.375 0 0 0 3.375 4.125v15.75A3.375 3.375 0 0 0 6.75 23.25h9.375A3.375 3.375 0 0 0 19.5 19.875v-5.625Z"
                                            />
                                        </svg>

                                    @endif

                                </div>


                                {{-- File Information --}}

                                <div class="min-w-0 flex-1">

                                    <p
                                        class="truncate text-sm font-medium text-slate-800"
                                        title="{{ $attachment->original_name }}"
                                    >
                                        {{ $attachment->original_name }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">

                                        @if ($attachment->file_size)

                                            {{ number_format($attachment->file_size / 1024 / 1024, 2) }} MB

                                        @endif

                                        @if ($attachment->mime_type)

                                            @if ($attachment->file_size)
                                                ·
                                            @endif

                                            {{ $attachment->mime_type }}

                                        @endif

                                    </p>

                                </div>


                                {{-- Open / Download --}}

                                <a
                                    href="{{ $fileUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                >
                                    Open
                                </a>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">

                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-5 w-5 text-slate-400"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M18.375 12.739 10.5 20.614a5.25 5.25 0 0 1-7.425-7.425l9.9-9.9a3.75 3.75 0 0 1 5.303 5.303l-9.9 9.9a2.25 2.25 0 0 1-3.182-3.182l8.132-8.132"
                                />
                            </svg>

                        </div>

                        <p class="mt-3 text-sm font-medium text-slate-700">
                            No attachments
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            No files have been attached to this assignment.
                        </p>

                    </div>

                @endif

            </div>

        </section>

    </aside>

</div>


{{-- ========================================================= --}}
{{-- STUDENT SUBMISSIONS --}}
{{-- ========================================================= --}}

<section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

    <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h2 class="font-semibold text-slate-900">
                Student Submissions
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Review and grade student submissions.
            </p>

        </div>


        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">

            {{ $assignment->submissions->count() }}

            {{ $assignment->submissions->count() === 1 ? 'submission' : 'submissions' }}

        </span>

    </div>


    <div class="p-6">

        @if ($assignment->submissions->isNotEmpty())

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead>

                        <tr class="text-left">

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Student
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Submitted
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Score
                            </th>

                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach ($assignment->submissions->sortByDesc('created_at') as $submission)

                            @php
                                $student = $submission->student;

                                $submittedAt =
                                    $submission->submitted_at
                                    ?? $submission->created_at;

                                $status =
                                    $submission->status
                                    ?? 'submitted';

                                $score =
                                    $submission->score
                                    ?? $submission->marks
                                    ?? null;
                            @endphp


                            <tr class="transition hover:bg-slate-50">

                                {{-- Student --}}

                                <td class="whitespace-nowrap px-4 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">

                                            {{ strtoupper(substr($student?->name ?? 'S', 0, 1)) }}

                                        </div>


                                        <div>

                                            <p class="text-sm font-semibold text-slate-900">

                                                {{ $student?->name ?? 'Unknown Student' }}

                                            </p>


                                            @if ($student?->email)

                                                <p class="mt-0.5 text-xs text-slate-500">
                                                    {{ $student->email }}
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- Submitted --}}

                                <td class="whitespace-nowrap px-4 py-4">

                                    @if ($submittedAt)

                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $submittedAt->format('M d, Y') }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            {{ $submittedAt->format('g:i A') }}
                                        </p>

                                    @else

                                        <span class="text-sm text-slate-400">
                                            Not available
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}

                                <td class="whitespace-nowrap px-4 py-4">

                                    @php
                                        $statusLabel = ucfirst(str_replace('_', ' ', $status));
                                    @endphp

                                    @if (in_array(strtolower($status), ['graded', 'marked', 'completed']))

                                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                            {{ $statusLabel }}
                                        </span>

                                    @elseif (in_array(strtolower($status), ['late', 'late_submitted']))

                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ $statusLabel }}
                                        </span>

                                    @else

                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                            {{ $statusLabel }}
                                        </span>

                                    @endif

                                </td>


                                {{-- Score --}}

                                <td class="whitespace-nowrap px-4 py-4">

                                    @if ($score !== null)

                                        <span class="text-sm font-semibold text-slate-900">
                                            {{ $score }}/{{ $assignment->total_marks }}
                                        </span>

                                    @else

                                        <span class="text-sm text-slate-400">
                                            Not graded
                                        </span>

                                    @endif

                                </td>


                                {{-- Action --}}

                                <td class="whitespace-nowrap px-4 py-4 text-right">

                                    <a
                                        href="{{ route(
                                            'teacher.courses.assignments.submissions.review',
                                            [$course, $assignment, $submission]
                                        ) }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                    >
                                        Review
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

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
                            d="M9 12.75 11.25 15 15 9.75"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>

                </div>


                <h3 class="mt-4 text-sm font-semibold text-slate-900">
                    No submissions yet
                </h3>


                <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                    When students submit this assignment,
                    their work will appear here for review and grading.
                </p>

            </div>

        @endif

    </div>

</section>


{{-- ========================================================= --}}
{{-- BOTTOM ACTIONS --}}
{{-- ========================================================= --}}

<div class="flex items-center justify-between">

    <a
        href="{{ route('teacher.courses.assignments.index', $course) }}"
        class="rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
    >
        ← Back to Assignments
    </a>


    <a
        href="{{ route('teacher.courses.assignments.edit', [$course, $assignment]) }}"
        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
    >
        Edit Assignment
    </a>

</div>
```

</div>

@endsection

@push('styles')

<style>

    .assignment-content {
        color: rgb(51 65 85);
        line-height: 1.8;
    }

    .assignment-content p {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .assignment-content h1 {
        margin-top: 1.75rem;
        margin-bottom: 1rem;
        font-size: 2rem;
        line-height: 1.2;
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
        margin-bottom: 0.5rem;
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
        margin-top: 0.4rem;
        margin-bottom: 0.4rem;
    }

    .assignment-content blockquote {
        margin: 1.25rem 0;
        border-left: 4px solid rgb(203 213 225);
        padding-left: 1rem;
        color: rgb(71 85 105);
    }

    .assignment-content a {
        color: rgb(37 99 235);
        text-decoration: underline;
    }

    .assignment-content img {
        display: block;
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
        border: 1px solid rgb(203 213 225);
        padding: 0.75rem;
        text-align: left;
        vertical-align: top;
    }

    .assignment-content th {
        background: rgb(241 245 249);
        font-weight: 600;
    }

</style>

@endpush

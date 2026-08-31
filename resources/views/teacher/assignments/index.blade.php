@extends('layouts.dashboard')

@section('title', 'Assignments · ' . $course->name)

@section('content')

<div class="space-y-8">

{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<section>

    <a
        href="{{ route('teacher.courses.show', $course) }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
    >
        ← Back to {{ $course->name }}
    </a>

    <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <p class="text-sm font-medium text-slate-500">
                {{ $course->subject?->name ?? 'Course' }}

                @if ($course->schoolClass)
                    · {{ $course->schoolClass->name }}
                @endif
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                Assignments
            </h1>

            <p class="mt-2 text-slate-500">
                Create, manage and track assignments for this course.
            </p>

        </div>

        <a
            href="{{ route('teacher.courses.assignments.create', $course) }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="currentColor"
                class="h-5 w-5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 5v14m-7-7h14"
                />
            </svg>

            Create Assignment
        </a>

    </div>

</section>


{{-- ========================================================= --}}
{{-- SUCCESS MESSAGE --}}
{{-- ========================================================= --}}

@if (session('success'))

    <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4">

        <p class="text-sm font-medium text-green-800">
            {{ session('success') }}
        </p>

    </div>

@endif


{{-- ========================================================= --}}
{{-- SUMMARY --}}
{{-- ========================================================= --}}

<section class="grid gap-4 sm:grid-cols-3">

    {{-- Total --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Total Assignments
        </p>

        <p class="mt-2 text-3xl font-semibold text-slate-900">
            {{ $assignments->count() }}
        </p>

        <p class="mt-1 text-sm text-slate-500">
            Created for this course
        </p>

    </div>


    {{-- Published --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Published
        </p>

        <p class="mt-2 text-3xl font-semibold text-slate-900">
            {{ $assignments->where('is_published', true)->count() }}
        </p>

        <p class="mt-1 text-sm text-slate-500">
            Visible to students
        </p>

    </div>


    {{-- Drafts --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <p class="text-sm font-medium text-slate-500">
            Drafts
        </p>

        <p class="mt-2 text-3xl font-semibold text-slate-900">
            {{ $assignments->where('is_published', false)->count() }}
        </p>

        <p class="mt-1 text-sm text-slate-500">
            Not visible to students
        </p>

    </div>

</section>


{{-- ========================================================= --}}
{{-- ASSIGNMENTS --}}
{{-- ========================================================= --}}

<section>

    <div class="mb-4">

        <h2 class="text-xl font-semibold text-slate-900">
            Your Assignments
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Manage assignments and prepare them for your students.
        </p>

    </div>


    @if ($assignments->isNotEmpty())

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="divide-y divide-slate-100">

                @foreach ($assignments as $assignment)

                    <div class="p-6 transition hover:bg-slate-50">

                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                            {{-- Assignment Information --}}
                            <div class="flex min-w-0 items-start gap-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-6 w-6 text-slate-700"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6.75 3h7.5L18.75 7.5v13.125A1.875 1.875 0 0 1 16.875 22.5h-10.5A1.875 1.875 0 0 1 4.5 20.625V4.875A1.875 1.875 0 0 1 6.375 3h.375Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.5 3v4.5h4.5"
                                        />
                                    </svg>

                                </div>


                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3 class="font-semibold text-slate-900">
                                            {{ $assignment->title }}
                                        </h3>


                                        @if ($assignment->is_published)

                                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                                Published
                                            </span>

                                        @else

                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                Draft
                                            </span>

                                        @endif

                                    </div>


                                    @if ($assignment->description)

                                        <p class="mt-1 max-w-2xl truncate text-sm text-slate-500">
                                            {{ $assignment->description }}
                                        </p>

                                    @else

                                        <p class="mt-1 text-sm text-slate-400">
                                            No description provided.
                                        </p>

                                    @endif


                                    {{-- Metadata --}}
                                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">

                                        <span>
                                            {{ $assignment->marks ?? 0 }} marks
                                        </span>


                                        @if ($assignment->due_at)

                                            <span>
                                                Due
                                                {{ $assignment->due_at->format('d M Y, g:i A') }}
                                            </span>

                                        @else

                                            <span>
                                                No due date
                                            </span>

                                        @endif


                                        @if ($assignment->created_at)

                                            <span>
                                                Created
                                                {{ $assignment->created_at->format('d M Y') }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- Actions --}}
                            <div class="flex shrink-0 items-center gap-2">

                                <a
                                    href="{{ route('teacher.courses.assignments.show', [$course, $assignment]) }}"
                                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                                >
                                    View
                                </a>


                                <a
                                    href="{{ route('teacher.courses.assignments.edit', [$course, $assignment]) }}"
                                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
                                >
                                    Edit
                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @else

        {{-- ================================================= --}}
        {{-- EMPTY STATE --}}
        {{-- ================================================= --}}

        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="h-7 w-7 text-slate-500"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6.75 3h7.5L18.75 7.5v13.125A1.875 1.875 0 0 1 16.875 22.5h-10.5A1.875 1.875 0 0 1 4.5 20.625V4.875A1.875 1.875 0 0 1 6.375 3h.375Z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13.5 3v4.5h4.5"
                    />
                </svg>

            </div>


            <h3 class="mt-5 text-lg font-semibold text-slate-900">
                No assignments yet
            </h3>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                Create your first assignment for this course.
                You can add instructions, marks, a due date and publish it when ready.
            </p>


            <a
                href="{{ route('teacher.courses.assignments.create', $course) }}"
                class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="h-5 w-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 5v14m-7-7h14"
                    />
                </svg>

                Create Assignment

            </a>

        </div>

    @endif

</section>

</div>

@endsection

@extends('layouts.dashboard')

@section('title', 'Student Dashboard')

@section('content')

<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- Welcome --}}
    {{-- ========================================================= --}}

    <section>

        <p class="text-sm font-medium text-slate-500">
            Student Dashboard
        </p>

        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">
            Welcome back, {{ $student->name }} 👋
        </h1>

        <p class="mt-2 text-slate-500">
            Here's what's happening with your learning today.
        </p>

    </section>


    {{-- ========================================================= --}}
    {{-- Summary Cards --}}
    {{-- ========================================================= --}}

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- My Courses --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        My Courses
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $courses->count() }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Active enrolled courses
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-5 w-5 text-slate-700"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6.75 3.75 10.5 12 14.25l8.25-3.75L12 6.75Z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 12.75v4.5c3.75 2.25 6.75 2.25 10.5 0v-4.5"
                        />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Due Soon --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Due Soon
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $dueSoonAssignments->count() }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Due within 7 days
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-5 w-5 text-slate-700"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6l4 2"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Pending --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Pending
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $pendingAssignmentCount }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Assignments to complete
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-5 w-5 text-slate-700"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5.25h6M9 3h6a1.5 1.5 0 0 1 1.5 1.5V6H18a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 21H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 6h1.5V4.5A1.5 1.5 0 0 1 9 3Z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 13h6M9 16h4"
                        />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Assignment Progress --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Assignment Progress
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $assignmentProgress }}%
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $gradedAssignmentCount }} graded
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-5 w-5 text-slate-700"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m9 12.75 2 2 4-4"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v12a2.25 2.25 0 0 1-2.25 2.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Z"
                        />

                    </svg>

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Progress Overview --}}
    {{-- ========================================================= --}}

    @if ($totalAssignments > 0)

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Assignment Progress
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $completedAssignments }} of {{ $totalAssignments }}
                        assignments completed.
                    </p>

                </div>

                <div class="text-sm font-medium text-slate-700">
                    {{ $assignmentProgress }}%
                </div>

            </div>

            <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">

                <div
                    class="h-full rounded-full bg-slate-900 transition-all"
                    style="width: {{ min($assignmentProgress, 100) }}%"
                ></div>

            </div>

            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-500">

                <span>
                    {{ $pendingAssignmentCount }} pending
                </span>

                <span>
                    {{ $submittedAssignmentCount }} submitted
                </span>

                <span>
                    {{ $gradedAssignmentCount }} graded
                </span>

            </div>

        </section>

    @endif


    {{-- ========================================================= --}}
    {{-- Continue Learning --}}
    {{-- ========================================================= --}}

    <section>

        <div class="mb-4 flex items-center justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    Continue Learning
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Pick up where you left off.
                </p>

            </div>

            @if ($courses->isNotEmpty())

                <a
                    href="{{ route('student.courses.index') }}"
                    class="text-sm font-medium text-slate-700 hover:text-slate-900"
                >
                    View all
                </a>

            @endif

        </div>


        @if ($courses->isNotEmpty())

            @php
                $continueCourse = $courses->first();
            @endphp

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-4">

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-slate-100">

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
                                    d="M12 6.75 3.75 10.5 12 14.25l8.25-3.75L12 6.75Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5.25 12v4.5c0 1.657 3.022 3 6.75 3s6.75-1.343 6.75-3V12"
                                />

                            </svg>

                        </div>

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Continue with
                            </p>

                            <h3 class="mt-1 font-semibold text-slate-900">
                                {{ $continueCourse->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">

                                {{ $continueCourse->subject?->name ?? 'Course' }}

                                @if ($continueCourse->schoolClass)
                                    · {{ $continueCourse->schoolClass->name }}
                                @endif

                            </p>

                        </div>

                    </div>

                    <a
                        href="{{ route('student.courses.show', $continueCourse) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                    >
                        Open Course
                    </a>

                </div>

            </div>

        @else

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h3 class="font-semibold text-slate-900">
                            Start Learning
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Join a course to start learning.
                        </p>

                    </div>

                    <a
                        href="{{ route('student.courses.join') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                    >
                        Join a Course
                    </a>

                </div>

            </div>

        @endif

    </section>


    {{-- ========================================================= --}}
    {{-- Upcoming Assignments --}}
    {{-- ========================================================= --}}

    <section>

        <div class="mb-4 flex items-end justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    Upcoming Assignments
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Work that needs your attention.
                </p>

            </div>

        </div>


        @if ($upcomingAssignments->isNotEmpty())

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="divide-y divide-slate-100">

                    @foreach ($upcomingAssignments as $assignment)

                        @php
                            $latestSubmission = $assignment->submissions->first();

                            $isSubmitted = $latestSubmission
                                && in_array(
                                    $latestSubmission->status,
                                    ['submitted', 'graded'],
                                    true
                                );

                            $isGraded = $latestSubmission
                                && $latestSubmission->status === 'graded';

                            $daysUntilDue = now()->diffInDays(
                                $assignment->due_at,
                                false
                            );
                        @endphp

                        <div class="p-5 transition hover:bg-slate-50">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div class="flex min-w-0 items-start gap-4">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-slate-700"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 5.25h6M9 3h6a1.5 1.5 0 0 1 1.5 1.5V6H18a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 21H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 6h1.5V4.5A1.5 1.5 0 0 1 9 3Z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 13h6M9 16h4"
                                            />

                                        </svg>

                                    </div>

                                    <div class="min-w-0">

                                        <h3 class="truncate font-semibold text-slate-900">
                                            {{ $assignment->title }}
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">

                                            {{ $assignment->course?->subject?->name ?? 'Course' }}

                                            @if ($assignment->course)
                                                · {{ $assignment->course->name }}
                                            @endif

                                        </p>

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">

                                            @if ($assignment->due_at)

                                                <span
                                                    class="
                                                        rounded-full
                                                        px-2.5
                                                        py-1
                                                        font-medium
                                                        {{ $daysUntilDue <= 1
                                                            ? 'bg-red-50 text-red-700'
                                                            : ($daysUntilDue <= 3
                                                                ? 'bg-amber-50 text-amber-700'
                                                                : 'bg-slate-100 text-slate-600') }}
                                                    "
                                                >
                                                    Due {{ $assignment->due_at->format('M j, Y g:i A') }}
                                                </span>

                                            @endif

                                            @if ($assignment->total_marks)

                                                <span class="text-slate-400">
                                                    {{ $assignment->total_marks }} marks
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <div class="flex shrink-0 items-center gap-3">

                                    @if ($isGraded)

                                        <span class="rounded-full bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700">
                                            Graded
                                        </span>

                                    @elseif ($isSubmitted)

                                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700">
                                            Submitted
                                        </span>

                                    @else

                                        <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">
                                            Pending
                                        </span>

                                    @endif

                                    <a
                                        href="{{ route('student.courses.show', $assignment->course) }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Open
                                    </a>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">

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

                <h3 class="mt-4 font-semibold text-slate-900">
                    You're all caught up
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    You don't have any upcoming assignments.
                </p>

            </div>

        @endif

    </section>


    {{-- ========================================================= --}}
    {{-- Your Courses --}}
    {{-- ========================================================= --}}

    <section>

        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    Your Courses
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Courses you are actively enrolled in.
                </p>

            </div>

            <a
                href="{{ route('student.courses.join') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800"
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

                Join Course

            </a>

        </div>


        @if ($courses->isNotEmpty())

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($courses as $course)

                    <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                        {{-- Course Header --}}

                        <div class="border-b border-slate-100 bg-slate-50 p-6">

                            <div class="flex items-start justify-between gap-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm">

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
                                            d="M12 6.75 3.75 10.5 12 14.25l8.25-3.75L12 6.75Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.25 12v4.5c0 1.657 3.022 3 6.75 3s6.75-1.343 6.75-3V12"
                                        />

                                    </svg>

                                </div>

                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                    Enrolled
                                </span>

                            </div>

                            <p class="mt-5 text-xs font-medium uppercase tracking-wide text-slate-400">
                                {{ $course->subject?->name ?? 'Course' }}
                            </p>

                            <h3 class="mt-1 text-lg font-semibold text-slate-900">
                                {{ $course->name }}
                            </h3>

                            @if ($course->schoolClass)

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $course->schoolClass->name }}
                                </p>

                            @endif

                        </div>


                        {{-- Course Body --}}

                        <div class="p-6">

                            @if ($course->description)

                                <p class="line-clamp-2 text-sm leading-6 text-slate-500">
                                    {{ $course->description }}
                                </p>

                            @else

                                <p class="text-sm text-slate-400">
                                    No course description available.
                                </p>

                            @endif


                            <div class="mt-5 flex items-center gap-5">

                                <div>

                                    <p class="text-lg font-semibold text-slate-900">
                                        {{ $course->resources_count }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Resources
                                    </p>

                                </div>

                                <div class="h-8 w-px bg-slate-200"></div>

                                <div>

                                    @php
                                        $courseAssignmentCount = $assignments
                                            ->where('course_id', $course->id)
                                            ->count();
                                    @endphp

                                    <p class="text-lg font-semibold text-slate-900">
                                        {{ $courseAssignmentCount }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Assignments
                                    </p>

                                </div>

                            </div>


                            <a
                                href="{{ route('student.courses.show', $course) }}"
                                class="mt-6 flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                            >
                                Open Course
                            </a>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">

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
                            d="M12 6.75 3.75 10.5 12 14.25l8.25-3.75L12 6.75Z"
                        />

                    </svg>

                </div>

                <h3 class="mt-4 font-semibold text-slate-900">
                    No courses yet
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    You don't have any active courses yet.
                    Join a course using the enrollment code provided by your teacher.
                </p>

                <a
                    href="{{ route('student.courses.join') }}"
                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                >
                    Join a Course
                </a>

            </div>

        @endif

    </section>


    {{-- ========================================================= --}}
    {{-- Recent Activity --}}
    {{-- ========================================================= --}}

    <section>

        <div class="mb-4">

            <h2 class="text-xl font-semibold text-slate-900">
                Recent Activity
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Your latest assignments and learning resources.
            </p>

        </div>


        <div class="grid gap-6 lg:grid-cols-2">

            {{-- ================================================= --}}
            {{-- Recent Assignment Activity --}}
            {{-- ================================================= --}}

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <h3 class="font-semibold text-slate-900">
                                Assignment Activity
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Your latest submissions.
                            </p>

                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-5 w-5 text-slate-700"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v12a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V6a2.25 2.25 0 0 1 2.25-2.25Z"
                                />

                            </svg>

                        </div>

                    </div>

                </div>


                @if ($recentSubmissions->isNotEmpty())

                    <div class="divide-y divide-slate-100">

                        @foreach ($recentSubmissions as $submission)

                            @php
                                $assignment = $submission->assignment;
                            @endphp

                            @if ($assignment)

                                <div class="px-6 py-4">

                                    <div class="flex items-start justify-between gap-4">

                                        <div class="min-w-0">

                                            <h4 class="truncate text-sm font-semibold text-slate-900">
                                                {{ $assignment->title }}
                                            </h4>

                                            <p class="mt-1 text-xs text-slate-500">

                                                {{ $assignment->course?->subject?->name ?? 'Course' }}

                                                @if ($assignment->course)
                                                    · {{ $assignment->course->name }}
                                                @endif

                                            </p>

                                            <p class="mt-2 text-xs text-slate-400">
                                                {{ $submission->updated_at?->diffForHumans() }}
                                            </p>

                                        </div>


                                        @if ($submission->status === 'graded')

                                            <div class="shrink-0 text-right">

                                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                                    Graded
                                                </span>

                                                @if ($submission->grade !== null)

                                                    <p class="mt-2 text-sm font-semibold text-slate-900">
                                                        {{ $submission->grade }}
                                                        @if ($assignment->total_marks)
                                                            / {{ $assignment->total_marks }}
                                                        @endif
                                                    </p>

                                                @endif

                                            </div>

                                        @elseif ($submission->status === 'submitted')

                                            <span class="shrink-0 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                                Submitted
                                            </span>

                                        @elseif ($submission->status === 'draft')

                                            <span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                Draft
                                            </span>

                                        @else

                                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                {{ ucfirst($submission->status) }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            @endif

                        @endforeach

                    </div>

                @else

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm font-medium text-slate-700">
                            No assignment activity yet
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Your submissions will appear here.

                        </p>

                    </div>

                @endif

            </div>


            {{-- ================================================= --}}
            {{-- Recent Resources --}}
            {{-- ================================================= --}}

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <h3 class="font-semibold text-slate-900">
                                Recent Resources
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Recently published learning materials.
                            </p>

                        </div>

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-5 w-5 text-slate-700"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A2.625 2.625 0 0 1 12 5.625v-1.5A3.375 3.375 0 0 0 8.625.75H7.5A2.25 2.25 0 0 0 5.25 3v18A2.25 2.25 0 0 0 7.5 23.25h9.75a2.25 2.25 0 0 0 2.25-2.25v-6.75Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 5.25V3.75"
                                />

                            </svg>

                        </div>

                    </div>

                </div>


                @if ($recentResources->isNotEmpty())

                    <div class="divide-y divide-slate-100">

                        @foreach ($recentResources as $resource)

                            <div class="px-6 py-4">

                                <div class="flex items-start gap-4">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-slate-700"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A2.625 2.625 0 0 1 12 5.625v-1.5A3.375 3.375 0 0 0 8.625.75H7.5A2.25 2.25 0 0 0 5.25 3v18A2.25 2.25 0 0 0 7.5 23.25h9.75a2.25 2.25 0 0 0 2.25-2.25v-6.75Z"
                                            />

                                        </svg>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <h4 class="truncate text-sm font-semibold text-slate-900">
                                            {{ $resource->title }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">

                                            {{ $resource->course?->subject?->name ?? 'Course' }}

                                            @if ($resource->course)
                                                · {{ $resource->course->name }}
                                            @endif

                                        </p>

                                        <div class="mt-2 flex items-center gap-2">

                                            @if ($resource->type)

                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                    {{ ucfirst($resource->type) }}
                                                </span>

                                            @endif

                                            <span class="text-xs text-slate-400">
                                                {{ $resource->published_at?->diffForHumans() }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm font-medium text-slate-700">
                            No recent resources
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            New learning materials will appear here.

                        </p>

                    </div>

                @endif

            </div>

        </div>

    </section>

</div>

@endsection
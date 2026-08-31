@extends('layouts.dashboard')

@section('title', 'Assignments')

@section('content')

<div class="mx-auto max-w-6xl space-y-8">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <section>

        <p class="text-sm font-medium text-slate-500">
            Student Learning
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
            Assignments
        </h1>

        <p class="mt-2 max-w-2xl text-slate-500">
            View assignments from the courses you are enrolled in.
        </p>

    </section>


    {{-- ========================================================= --}}
    {{-- ASSIGNMENTS --}}
    {{-- ========================================================= --}}

    @if ($assignments->isEmpty())

        <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">

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
                        d="M9 12.75 11.25 15 15 9.75M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9Z"
                    />
                </svg>

            </div>

            <h2 class="mt-5 text-lg font-semibold text-slate-900">
                No assignments yet
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                Your teachers have not published any assignments for your courses yet.
            </p>

            <a
                href="{{ route('student.courses.index') }}"
                class="mt-6 inline-flex items-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
            >
                View My Courses
            </a>

        </section>

    @else

        <div class="grid gap-5 md:grid-cols-2">

            @foreach ($assignments as $assignment)

                @php
                    $isOverdue =
                        $assignment->due_at &&
                        $assignment->due_at->isPast();

                    $course = $assignment->course;
                @endphp

                <article
                    class="group rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
                >

                    <div class="p-6">

                        {{-- Course information --}}

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    {{ $course->subject?->name ?? 'Course' }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $course->name }}

                                    @if ($course->schoolClass)
                                        · {{ $course->schoolClass->name }}
                                    @endif
                                </p>

                            </div>


                            {{-- Status --}}

                            @if ($isOverdue)

                                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                    Overdue
                                </span>

                            @else

                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Open
                                </span>

                            @endif

                        </div>


                        {{-- Title --}}

                        <h2 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">

                            {{ $assignment->title }}

                        </h2>


                        {{-- Description --}}

                        @if ($assignment->description)

                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500">
                                {{ $assignment->description }}
                            </p>

                        @else

                            <p class="mt-3 text-sm text-slate-400">
                                No description provided.
                            </p>

                        @endif


                        {{-- Assignment metadata --}}

                        <div class="mt-6 grid grid-cols-2 gap-3">

                            <div class="rounded-xl bg-slate-50 p-3">

                                <p class="text-xs font-medium text-slate-400">
                                    Total Marks
                                </p>

                                <p class="mt-1 font-semibold text-slate-900">
                                    {{ $assignment->total_marks }}
                                </p>

                            </div>


                            <div class="rounded-xl bg-slate-50 p-3">

                                <p class="text-xs font-medium text-slate-400">
                                    Due
                                </p>

                                @if ($assignment->due_at)

                                    <p class="mt-1 text-sm font-semibold text-slate-900">
                                        {{ $assignment->due_at->format('d M Y') }}
                                    </p>

                                @else

                                    <p class="mt-1 text-sm font-semibold text-slate-500">
                                        No deadline
                                    </p>

                                @endif

                            </div>

                        </div>


                        {{-- Footer --}}

                        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">

                            @if ($assignment->due_at)

                                <p class="text-xs text-slate-400">

                                    Due
                                    {{ $assignment->due_at->format('d M Y, g:i A') }}

                                </p>

                            @else

                                <p class="text-xs text-slate-400">
                                    No deadline
                                </p>

                            @endif


                            <a
                                href="{{ route('student.assignments.show', $assignment) }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                            >

                                Open Assignment

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.7"
                                    stroke="currentColor"
                                    class="h-4 w-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                    />
                                </svg>

                            </a>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</div>

@endsection
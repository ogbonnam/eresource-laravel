@extends('layouts.dashboard')

@section('title', 'Teacher Dashboard')

@section('content')

<div class="space-y-8">

    {{-- Header --}}
    <section>

        <p class="text-sm font-medium text-slate-500">
            Teacher Dashboard
        </p>

        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">
            Welcome back, {{ $teacher->name }} 👋
        </h1>

        <p class="mt-2 text-slate-500">
            Manage your classes, students, resources, assignments, and
            student progress.
        </p>

    </section>


    {{-- Summary --}}
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">

        {{-- Classes --}}
        <a
            href="{{ route('teacher.courses.index') }}"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
        >

            <p class="text-sm font-medium text-slate-500">
                My Classes
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $classCount }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Classes assigned
            </p>

        </a>


        {{-- Students --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm font-medium text-slate-500">
                Students
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $studentCount }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Unique students
            </p>

        </div>


        {{-- Resources --}}
        <a
            href="#recent-resources"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
        >

            <p class="text-sm font-medium text-slate-500">
                Resources
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $resourceCount }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Learning resources
            </p>

        </a>


        {{-- Assignments --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm font-medium text-slate-500">
                Assignments
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $assignmentCount }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Total assignments
            </p>

        </div>


        {{-- Pending grading --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm font-medium text-slate-500">
                To Grade
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $pendingGradingCount }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Awaiting grading
            </p>

        </div>

    </section>


    {{-- Grading summary --}}
    <section class="grid gap-4 md:grid-cols-2">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Pending Grading
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $pendingGradingCount }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Student submissions waiting for review
                    </p>

                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    !
                </div>

            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Graded Submissions
                    </p>

                    <p class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $gradedSubmissionCount }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Student submissions already graded
                    </p>

                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    ✓
                </div>

            </div>

        </div>

    </section>


    {{-- My Classes --}}
    <section>

        <div class="mb-4 flex items-end justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    My Classes
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Your courses, students, resources, and assignments.
                </p>

            </div>

            <a
                href="{{ route('teacher.courses.index') }}"
                class="text-sm font-medium text-slate-700 hover:text-slate-900"
            >
                View all →
            </a>

        </div>


        @if ($courses->isEmpty())

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">

                <h3 class="font-semibold text-slate-900">
                    No classes yet
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                    Once classes are assigned to you, you will be able to
                    manage students and learning resources from here.
                </p>

            </div>

        @else

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

                @foreach ($courses as $course)

                    <a
                        href="{{ route('teacher.courses.show', $course) }}"
                        class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
                    >

                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0">

                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    {{ $course->subject?->name ?? 'Course' }}
                                </p>

                                <h3 class="mt-1 truncate text-lg font-semibold text-slate-900">
                                    {{ $course->name }}
                                </h3>

                            </div>

                            <span class="text-slate-400 transition group-hover:text-slate-900">
                                →
                            </span>

                        </div>


                        @if ($course->schoolClass)

                            <p class="mt-3 text-sm text-slate-500">
                                {{ $course->schoolClass->name }}
                            </p>

                        @endif


                        <div class="mt-5 flex items-center gap-6 border-t border-slate-100 pt-4">

                            <div>

                                <p class="text-lg font-semibold text-slate-900">
                                    {{ $course->resources_count }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    Resources
                                </p>

                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        @endif

    </section>


    {{-- Recent activity --}}
    <section
        id="recent-resources"
        class="grid gap-6 lg:grid-cols-2"
    >

        {{-- Recent Resources --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Recent Resources
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Resources you recently created or updated.
                    </p>

                </div>

                @if ($resourceCount > 0)

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $resourceCount }}
                    </span>

                @endif

            </div>


            <div class="divide-y divide-slate-100">

                @forelse ($recentResources as $resource)

                    <a
                        href="{{ route(
                            'teacher.courses.resources.show',
                            [$resource->course, $resource]
                        ) }}"
                        class="block px-6 py-4 transition hover:bg-slate-50"
                    >

                        <div class="flex items-center justify-between gap-4">

                            <div class="min-w-0">

                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $resource->title }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">

                                    {{ $resource->course?->name ?? 'Course' }}

                                    ·

                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $resource->type
                                        )
                                    ) }}

                                </p>

                            </div>


                            <div class="shrink-0 text-right">

                                @if ($resource->is_published)

                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Published
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Draft
                                    </span>

                                @endif

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $resource->updated_at?->diffForHumans() }}
                                </p>

                            </div>

                        </div>

                    </a>

                @empty

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm font-medium text-slate-700">
                            No resources yet
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Create a resource inside one of your courses.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>


        {{-- Student Activity --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Student Activity
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Recent submissions from your students.
                    </p>

                </div>

                @if ($recentStudentActivity->isNotEmpty())

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $recentStudentActivity->count() }}
                    </span>

                @endif

            </div>


            <div class="divide-y divide-slate-100">

                @forelse ($recentStudentActivity as $activity)

                    <div class="px-6 py-4">

                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0">

                                {{-- Student --}}
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $activity->student?->name ?? 'Student' }}
                                </p>


                                {{-- Assignment --}}
                                <p class="mt-1 truncate text-sm text-slate-600">

                                    submitted

                                    <span class="font-medium text-slate-900">
                                        {{ $activity->assignment?->title ?? 'Assignment' }}
                                    </span>

                                </p>


                                {{-- Course --}}
                                <p class="mt-1 text-xs text-slate-400">

                                    {{ $activity->assignment?->course?->name ?? 'Course' }}

                                    ·

                                    {{ $activity->updated_at?->diffForHumans() }}

                                </p>

                            </div>


                            {{-- Status --}}
                            <div class="shrink-0 text-right">

                                @if ($activity->status === 'graded')

                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Graded
                                    </span>

                                    @if ($activity->grade !== null)

                                        <p class="mt-1 text-xs font-semibold text-slate-600">
                                            {{ $activity->grade }}
                                            /
                                            {{ $activity->assignment?->total_marks ?? 100 }}
                                        </p>

                                    @endif

                                @elseif ($activity->status === 'submitted')

                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Needs grading
                                    </span>

                                @elseif ($activity->status === 'draft')

                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                                        Draft
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                                        {{ ucfirst($activity->status) }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm font-medium text-slate-700">
                            No student activity yet
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Student assignment submissions will appear here.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </section>


    {{-- Recent Assignments --}}
    <section>

        <div class="mb-4 flex items-end justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    Recent Assignments
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Your latest assignments and submission activity.
                </p>

            </div>

        </div>


        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="divide-y divide-slate-100">

                @forelse ($recentAssignments as $assignment)

                    <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="min-w-0">

                            <p class="font-semibold text-slate-900">
                                {{ $assignment->title }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">

                                {{ $assignment->course?->name ?? 'Course' }}

                                @if ($assignment->due_at)

                                    · Due
                                    {{ $assignment->due_at->format('d M Y, g:i A') }}

                                @endif

                            </p>

                        </div>


                        <div class="flex items-center gap-6">

                            <div class="text-right">

                                <p class="text-lg font-semibold text-slate-900">
                                    {{ $assignment->submissions_count }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    Submissions
                                </p>

                            </div>


                            <a
                                href="{{ route(
                                    'teacher.courses.assignments.show',
                                    [
                                        $assignment->course,
                                        $assignment
                                    ]
                                ) }}"
                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            >
                                View
                            </a>

                        </div>

                    </div>

                @empty

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm font-medium text-slate-700">
                            No assignments yet
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Create an assignment from one of your courses.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </section>

</div>

@endsection
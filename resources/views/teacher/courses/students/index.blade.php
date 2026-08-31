@extends('layouts.dashboard')

@section('title', 'Students · ' . $course->name)

@section('content')

<div class="mx-auto max-w-6xl space-y-8">

    {{-- Header --}}
    <section>

        <a
            href="{{ route('teacher.courses.show', $course) }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
        >
            ← Back to {{ $course->name }}
        </a>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <p class="text-sm font-medium text-slate-500">
                    {{ $course->subject?->name }}

                    @if ($course->schoolClass)
                        · {{ $course->schoolClass->name }}
                    @endif
                </p>

                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                    Students
                </h1>

                <p class="mt-2 text-slate-500">
                    Manage students enrolled in this course.
                </p>

            </div>

            <a
                href="{{ route('teacher.courses.students.create', $course) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
            >
                <span class="text-lg leading-none">+</span>
                Add Student
            </a>

        </div>

    </section>


    {{-- Flash messages --}}
    @if (session('success'))

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('success') }}
        </div>

    @endif

    @if (session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            {{ session('error') }}
        </div>

    @endif


    {{-- Summary --}}
    <section class="grid gap-4 sm:grid-cols-3">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm font-medium text-slate-500">
                Total Enrollments
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $enrollments->count() }}
            </p>

        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm font-medium text-slate-500">
                Active Students
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $enrollments->where('status', 'active')->count() }}
            </p>

        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm font-medium text-slate-500">
                Completed
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $enrollments->where('status', 'completed')->count() }}
            </p>

        </div>

    </section>


    {{-- Students --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <h2 class="font-semibold text-slate-900">
                Enrolled Students
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Students currently or previously enrolled in this course.
            </p>

        </div>


        @if ($enrollments->isEmpty())

            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl">
                    👥
                </div>

                <h3 class="mt-4 font-semibold text-slate-900">
                    No students yet
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                    Add students to this course so they can access
                    published learning resources.
                </p>

                <a
                    href="{{ route('teacher.courses.students.create', $course) }}"
                    class="mt-5 inline-flex rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
                >
                    Add Student
                </a>

            </div>

        @else

            <div class="divide-y divide-slate-100">

                @foreach ($enrollments as $enrollment)

                    <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                                {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                            </div>

                            <div>

                                <p class="font-medium text-slate-900">
                                    {{ $enrollment->user->name }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    {{ $enrollment->user->email }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center gap-3">

                            @if ($enrollment->status === 'active')

                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Active
                                </span>

                            @elseif ($enrollment->status === 'completed')

                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    Completed
                                </span>

                            @else

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Dropped
                                </span>

                            @endif


                            @if ($enrollment->status === 'active')

                                <form
                                    method="POST"
                                    action="{{ route('teacher.courses.students.destroy', [$course, $enrollment->user]) }}"
                                    onsubmit="return confirm('Remove this student from the course?');"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                                    >
                                        Remove
                                    </button>

                                </form>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection
@extends('layouts.dashboard')

@section('title', $course->name)

@section('content')

<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <section>

        <a
            href="{{ route('teacher.courses.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900"
        >
            ← Back to my courses
        </a>

        <div class="mt-6">

            <p class="text-sm font-medium text-slate-500">
                {{ $course->subject?->name }}
                ·
                {{ $course->schoolClass?->name }}
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                {{ $course->name }}
            </h1>

            @if ($course->description)

                <p class="mt-3 max-w-2xl leading-7 text-slate-500">
                    {{ $course->description }}
                </p>

            @endif

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Course Summary --}}
    {{-- ========================================================= --}}

    <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Resources --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm text-slate-500">
                Resources
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $course->resources->count() }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Learning materials
            </p>

        </div>


        {{-- Students --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm text-slate-500">
                Students
            </p>

            <p class="mt-2 text-3xl font-semibold text-slate-900">
                {{ $course->students()->wherePivot('status', 'active')->count() }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Active students
            </p>

        </div>


        {{-- Pending Requests --}}
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">

            <p class="text-sm font-medium text-amber-700">
                Pending Requests
            </p>

            <p class="mt-2 text-3xl font-semibold text-amber-900">
                {{ $course->enrollments()->where('status', 'pending')->count() }}
            </p>

            <p class="mt-1 text-sm text-amber-700">
                Awaiting approval
            </p>

        </div>


        {{-- Status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <p class="text-sm text-slate-500">
                Status
            </p>

            <p class="mt-2 text-lg font-semibold text-slate-900">
                {{ $course->is_active ? 'Active' : 'Inactive' }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Course availability
            </p>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Student Enrollment --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Student Enrollment
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Share the enrollment code with students so they can
                        request to join this course.
                    </p>

                </div>


                {{-- Pending Requests Button --}}
                @php
                    $pendingCount = $course->enrollments()
                        ->where('status', 'pending')
                        ->count();
                @endphp

                <a
                    href="{{ route('teacher.courses.students.pending', $course) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium transition
                    {{ $pendingCount > 0
                        ? 'bg-amber-500 text-white hover:bg-amber-600'
                        : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                    }}"
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
                            d="M18 8.25a6 6 0 0 1-12 0 6 6 0 0 1 12 0Z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4.5 19.5a7.5 7.5 0 0 1 15 0"
                        />
                    </svg>

                    Review Pending Requests

                    @if ($pendingCount > 0)

                        <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-white px-2 py-0.5 text-xs font-bold text-amber-600">
                            {{ $pendingCount }}
                        </span>

                    @endif

                </a>

            </div>

        </div>


        <div class="p-6">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                {{-- Enrollment Code --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Enrollment Code
                    </p>

                    @if ($course->enrollment_code)

                        <div class="mt-2 flex flex-wrap items-center gap-3">

                            <span
                                class="rounded-xl bg-slate-100 px-5 py-3 font-mono text-2xl font-bold tracking-[0.25em] text-slate-900"
                            >
                                {{ $course->enrollment_code }}
                            </span>

                            <button
                                type="button"
                                onclick="
                                    navigator.clipboard.writeText('{{ $course->enrollment_code }}').then(() => {
                                        this.innerText = 'Copied!';
                                        setTimeout(() => this.innerText = 'Copy', 1500);
                                    });
                                "
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            >
                                Copy
                            </button>

                        </div>

                    @else

                        <p class="mt-2 text-sm text-slate-500">
                            No enrollment code has been generated for this course.
                        </p>

                    @endif

                </div>


                {{-- Current Students --}}
                <div class="rounded-xl bg-slate-50 px-5 py-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Enrolled Students
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-slate-900">
                        {{ $course->students()->wherePivot('status', 'active')->count() }}
                    </p>

                </div>

            </div>


            {{-- Enrollment Instructions --}}
            <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 px-5 py-4">

                <div class="flex gap-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m0 3h.008v.008H12v-.008ZM10.615 3.891 2.91 17.25a1.875 1.875 0 0 0 1.624 2.813h14.932a1.875 1.875 0 0 0 1.624-2.813L13.385 3.891a1.59 1.59 0 0 0-2.77 0Z"
                        />
                    </svg>

                    <div>

                        <p class="text-sm font-medium text-blue-900">
                            How enrollment works
                        </p>

                        <p class="mt-1 text-sm leading-6 text-blue-800">
                            Give students the enrollment code.
                            They enter it from their Student Dashboard
                            and submit a request to join this course.
                            You can then approve or reject each request.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Resources --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-semibold text-slate-900">
                    Resources
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Learning materials for this course.
                </p>

            </div>


            <a
                href="{{ route('teacher.courses.resources.create', $course) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
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
                        d="M12 5v14M5 12h14"
                    />
                </svg>

                Add Resource

            </a>

        </div>


        <div class="p-6">

            @if ($course->resources->isEmpty())

                {{-- Empty State --}}
                <div class="py-10 text-center">

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
                                d="M19.5 14.25v-7.5a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5A2.25 2.25 0 0 0 6.75 19.5h10.5a2.25 2.25 0 0 0 2.25-2.25v-3"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 3v4.5a1.5 1.5 0 0 0 1.5 1.5H21"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 13.5h6M9 16.5h3"
                            />

                        </svg>

                    </div>


                    <p class="mt-4 font-medium text-slate-700">
                        No resources yet
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Add your first learning resource to this course.
                    </p>


                    <a
                        href="{{ route('teacher.courses.resources.create', $course) }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
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
                                d="M12 5v14M5 12h14"
                            />
                        </svg>

                        Add Resource

                    </a>

                </div>


            @else

                {{-- Resource List --}}
                <div class="divide-y divide-slate-100">

                    @foreach ($course->resources as $resource)

                        <div class="flex flex-col gap-4 py-5 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex min-w-0 items-center gap-4">

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
                                            d="M19.5 14.25v-7.5a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5A2.25 2.25 0 0 0 6.75 19.5h10.5a2.25 2.25 0 0 0 2.25-2.25v-3"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 3v4.5a1.5 1.5 0 0 0 1.5 1.5H21"
                                        />

                                    </svg>

                                </div>


                                <div class="min-w-0">

                                    <a
                                        href="{{ route('teacher.courses.resources.show', [$course, $resource]) }}"
                                        class="truncate font-medium text-slate-900 hover:text-slate-600"
                                    >
                                        {{ $resource->title }}
                                    </a>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ ucfirst(str_replace('_', ' ', $resource->type)) }}
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-center gap-3">

                                @if ($resource->is_published)

                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                        Published
                                    </span>

                                @else

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                        Draft
                                    </span>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Course Information --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="font-semibold text-slate-900">
            Course Information
        </h2>

        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            <div>

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Subject
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ $course->subject?->name ?? 'Not assigned' }}
                </p>

            </div>


            <div>

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Class
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ $course->schoolClass?->name ?? 'Not assigned' }}
                </p>

            </div>


            <div>

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Course Code
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ $course->code ?? 'No code' }}
                </p>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Course Students --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-semibold text-slate-900">
                    Students
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage students enrolled in this course.
                </p>

            </div>


            <a
                href="{{ route('teacher.courses.students.index', $course) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                Manage Students
            </a>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Delete Course --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-red-200 bg-red-50/40 p-6">

        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-semibold text-red-900">
                    Delete Course
                </h2>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-red-700">
                    Deleting this course will also delete its resources,
                    enrollments, and other course data. This action cannot
                    be undone.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('teacher.courses.destroy', $course) }}"
                onsubmit="return confirm('Delete this course? This will also delete its resources and enrollments. This action cannot be undone.')"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-4 w-4"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 7.5h12m-10.5 0v10.75A1.75 1.75 0 0 0 9.25 20h5.5a1.75 1.75 0 0 0 1.75-1.75V7.5M9.75 7.5V5.25A1.25 1.25 0 0 1 11 4h2a1.25 1.25 0 0 1 1.25 1.25V7.5"
                        />
                    </svg>

                    Delete Course

                </button>

            </form>

        </div>

    </section>

</div>

@endsection
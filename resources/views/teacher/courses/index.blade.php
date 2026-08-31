@extends('layouts.dashboard')

@section('title', 'My Courses')

@section('content')

<div class="space-y-8">

```
{{-- Header --}}
<section>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <p class="text-sm font-medium text-slate-500">
                Teacher
            </p>

            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">
                My Courses
            </h1>

            <p class="mt-2 text-slate-500">
                Manage your courses, students, and learning resources.
            </p>

        </div>

        <a
            href="{{ route('teacher.courses.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800"
        >

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
                    d="M12 4.5v15m7.5-7.5h-15"
                />
            </svg>

            Create Course

        </a>

    </div>

</section>


{{-- Success message --}}
@if (session('success'))

    <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
        {{ session('success') }}
    </div>

@endif


{{-- Error message --}}
@if (session('error'))

    <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ session('error') }}
    </div>

@endif


{{-- Courses --}}
@if ($courses->isNotEmpty())

    <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

        @foreach ($courses as $course)

            <div
                class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
            >

                {{-- Course header --}}
                <a
                    href="{{ route('teacher.courses.show', $course) }}"
                    class="flex h-32 items-center justify-center bg-slate-100"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-10 w-10 text-slate-400 transition group-hover:text-slate-600"
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

                </a>


                <div class="p-5">

                    {{-- Course information --}}
                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <a
                                href="{{ route('teacher.courses.show', $course) }}"
                                class="block"
                            >

                                <h2 class="font-semibold text-slate-900 transition hover:text-slate-600">
                                    {{ $course->name }}
                                </h2>

                            </a>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ $course->subject?->name ?? 'No subject' }}
                                ·
                                {{ $course->schoolClass?->name ?? 'No class' }}
                            </p>

                        </div>


                        {{-- Resource count --}}
                        <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            {{ $course->resources_count }}
                            {{ Str::plural('resource', $course->resources_count) }}
                        </span>

                    </div>


                    {{-- Description --}}
                    @if ($course->description)

                        <p class="mt-4 line-clamp-2 text-sm leading-6 text-slate-500">
                            {{ $course->description }}
                        </p>

                    @else

                        <p class="mt-4 text-sm text-slate-400">
                            No description
                        </p>

                    @endif


                    {{-- Course actions --}}
                    <div class="mt-5 flex flex-wrap items-center gap-2">

                        {{-- Open course --}}
                        <a
                            href="{{ route('teacher.courses.show', $course) }}"
                            class="inline-flex items-center gap-1 rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
                        >

                            Open Course

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
                                    d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                />
                            </svg>

                        </a>


                        {{-- Manage students --}}
                        <a
                            href="{{ route('teacher.courses.students.index', $course) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >

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
                                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.37 9.37 0 0 0 2.625-.372M15 19.128v-.003c0-1.386 1.122-2.51 2.507-2.51h.236c1.385 0 2.507 1.124 2.507 2.51v.003M15 19.128a9.37 9.37 0 0 1-6 0m6 0v-.003c0-1.386-1.122-2.51-2.507-2.51h-.236C10.872 16.615 9.75 17.739 9.75 19.125v.003m-6-4.5a9.37 9.37 0 0 0 6 0m-6 0v-.003c0-1.386 1.122-2.51 2.507-2.51h.236c1.385 0 2.507 1.124 2.507 2.51v.003m-6 0a9.38 9.38 0 0 1-2.625.372 9.37 9.37 0 0 1-2.625-.372"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                />
                            </svg>

                            Students
                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </section>

@else

    {{-- Empty state --}}
    <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">

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
                    d="M12 6.75 3.75 10.5 12 14.25l8.25-3.75L12 6.75Z"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5.25 12v4.5c0 1.657 3.022 3 6.75 3s6.75-1.343 6.75-3V12"
                />

            </svg>

        </div>


        <h2 class="mt-5 text-lg font-semibold text-slate-900">
            No courses yet
        </h2>


        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            Create your first course to start adding learning resources
            and enrolling students.
        </p>


        <a
            href="{{ route('teacher.courses.create') }}"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800"
        >
            Create your first course
        </a>

    </section>

@endif
```

</div>

@endsection

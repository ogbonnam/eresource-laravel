@extends('layouts.dashboard')

@section('title', 'My Courses')

@section('content')

<div class="space-y-8">

    <div>
        <p class="text-sm font-medium text-slate-500">
            Learning
        </p>

        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">
            My Courses
        </h1>

        <p class="mt-2 text-slate-500">
            Courses you are currently enrolled in.
        </p>
    </div>

    @if ($courses->isEmpty())

        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">

            <h2 class="font-semibold text-slate-900">
                No courses yet
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                You are not currently enrolled in any courses.
            </p>

        </div>

    @else

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            @foreach ($courses as $course)

                <a
                    href="{{ route('student.courses.show', $course) }}"
                    class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">

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
                                d="M12 6.75 3.75 10.5 12 14.25l8.25-3.75L20.25 10.5 12 6.75Z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 12.75v4.5c3.75 2.25 6.75 3 5.25 3s5.25-1.343 5.25-3v-4.5"
                            />
                        </svg>

                    </div>

                    <h2 class="mt-5 font-semibold text-slate-900 group-hover:text-slate-700">
                        {{ $course->name }}
                    </h2>

                    @if ($course->subject)
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $course->subject->name }}
                        </p>
                    @endif

                    <div class="mt-5 flex items-center justify-between text-sm">

                        <span class="text-slate-500">
                            {{ $course->resources_count }} resources
                        </span>

                        <span class="font-medium text-slate-700">
                            Open →
                        </span>

                    </div>

                </a>

            @endforeach

        </div>

    @endif

</div>

@endsection
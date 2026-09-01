@extends('layouts.dashboard')

@section('title', $course->name)

@section('content')

<div class="space-y-8">

    {{-- =========================================================
        COURSE HEADER
    ========================================================== --}}

    <div>

        <a
            href="{{ route('student.courses.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium
                   text-slate-500 transition hover:text-indigo-600"
        >
            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"
                />
            </svg>

            My Courses
        </a>


        {{-- Course Header --}}
        <div class="mt-6 overflow-hidden rounded-3xl
                    bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700
                    shadow-xl">

            <div class="p-7 sm:p-9">

                <div class="flex flex-col gap-6 sm:flex-row
                            sm:items-center sm:justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-[0.2em]
                                  text-indigo-200">
                            Course
                        </p>

                        <h1 class="mt-2 text-3xl font-bold tracking-tight
                                   text-white sm:text-4xl">
                            {{ $course->name }}
                        </h1>


                        <div class="mt-4 flex flex-wrap items-center gap-3
                                    text-sm text-indigo-100">

                            @if ($course->subject)

                                <span>
                                    {{ $course->subject->name }}
                                </span>

                            @endif


                            @if ($course->subject && $course->schoolClass)

                                <span class="text-indigo-300">
                                    •
                                </span>

                            @endif


                            @if ($course->schoolClass)

                                <span>
                                    {{ $course->schoolClass->name }}
                                </span>

                            @endif

                        </div>


                        @if ($course->description)

                            <p class="mt-5 max-w-2xl text-sm leading-6
                                      text-indigo-100 sm:text-base">
                                {{ $course->description }}
                            </p>

                        @endif

                    </div>


                    {{-- Resource count --}}
                    <div class="shrink-0 rounded-2xl bg-white/10
                                px-7 py-5 text-center backdrop-blur-sm">

                        <div class="text-3xl font-bold text-white">
                            {{ $course->resources->count() }}
                        </div>

                        <div class="mt-1 text-xs font-semibold uppercase
                                    tracking-wider text-indigo-200">
                            Resources
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
        RESOURCES
    ========================================================== --}}

    <section>

        <div class="mb-6">

            <h2 class="text-2xl font-bold text-slate-900">
                Resources
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Select a resource to start learning.
            </p>

        </div>


        @if ($course->resources->isEmpty())

            {{-- Empty State --}}
            <div class="rounded-3xl border border-dashed
                        border-slate-300 bg-white px-6 py-16
                        text-center shadow-sm">

                <div class="mx-auto flex h-16 w-16 items-center
                            justify-center rounded-2xl bg-slate-100">

                    <svg
                        class="h-8 w-8 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="1.7"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6.253v13m0-13
                               C10.832 5.477 9.246 5
                               7.5 5S4.168 5.477 3 6.253v13
                               C4.168 18.477 5.754 18
                               7.5 18s3.332.477 4.5 1.253"
                        />
                    </svg>

                </div>


                <h3 class="mt-5 font-semibold text-slate-800">
                    No resources yet
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    Your teacher has not added any resources.
                </p>

            </div>


        @else


            {{-- =================================================
                 TILE GRID
            ================================================== --}}

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2
                        lg:grid-cols-3 xl:grid-cols-4">


                @foreach ($course->resources as $resource)

                    <a
                        href="{{ route('student.resources.show', $resource) }}"
                        class="group relative flex min-h-[260px] flex-col
                               overflow-hidden rounded-3xl border
                               border-slate-200 bg-white shadow-sm
                               transition-all duration-300
                               hover:-translate-y-1
                               hover:border-indigo-200
                               hover:shadow-xl"
                    >

                        {{-- Decorative top section --}}
                        <div class="relative h-28 overflow-hidden
                                    bg-gradient-to-br from-indigo-50
                                    via-violet-50 to-white">

                            {{-- Decorative circles --}}
                            <div class="absolute -right-8 -top-10
                                        h-32 w-32 rounded-full
                                        bg-indigo-100/70"></div>

                            <div class="absolute -bottom-12 -left-8
                                        h-28 w-28 rounded-full
                                        bg-violet-100/60"></div>


                            {{-- Resource icon --}}
                            <div class="absolute left-5 top-5 flex h-14 w-14
                                        items-center justify-center
                                        rounded-2xl bg-white text-indigo-600
                                        shadow-sm ring-1 ring-indigo-100
                                        transition-all duration-300
                                        group-hover:scale-105
                                        group-hover:bg-indigo-600
                                        group-hover:text-white">

                                <svg
                                    class="h-7 w-7"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5
                                           a2 2 0 012-2z"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M14 3v6h6"
                                    />
                                </svg>

                            </div>


                            {{-- Resource label --}}
                            <div class="absolute right-4 top-4">

                                <span class="rounded-full bg-white/90
                                             px-3 py-1 text-[10px]
                                             font-bold uppercase
                                             tracking-wider text-slate-500
                                             shadow-sm">
                                    Resource
                                </span>

                            </div>

                        </div>


                        {{-- Tile content --}}
                        <div class="flex flex-1 flex-col p-5">

                            <h3 class="line-clamp-2 text-lg font-bold
                                       leading-6 text-slate-900
                                       transition-colors
                                       group-hover:text-indigo-600">

                                {{ $resource->title }}

                            </h3>


                            @if ($resource->description)

                                <p class="mt-3 line-clamp-3 text-sm
                                          leading-5 text-slate-500">
                                    {{ $resource->description }}
                                </p>

                            @else

                                <p class="mt-3 text-sm leading-5 text-slate-400">
                                    Learning material for this course.
                                </p>

                            @endif


                            {{-- Bottom action --}}
                            <div class="mt-auto pt-5">

                                <div class="flex items-center justify-between
                                            border-t border-slate-100 pt-4">

                                    <span class="text-xs font-medium
                                                 text-slate-400">
                                        Open resource
                                    </span>


                                    <span class="flex h-9 w-9 items-center
                                                 justify-center rounded-full
                                                 bg-slate-100 text-slate-500
                                                 transition-all duration-200
                                                 group-hover:bg-indigo-600
                                                 group-hover:text-white">

                                        <svg
                                            class="h-4 w-4 transition-transform
                                                   group-hover:translate-x-0.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>

                                    </span>

                                </div>

                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection

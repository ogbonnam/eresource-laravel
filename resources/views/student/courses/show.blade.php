@extends('layouts.dashboard')

@section('title', $course->name)

@section('content')

<div class="space-y-8">

    <div>

        <a
            href="{{ route('student.courses.index') }}"
            class="text-sm font-medium text-slate-500 hover:text-slate-900"
        >
            ← My Courses
        </a>

        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">
            {{ $course->name }}
        </h1>

        <div class="mt-2 flex flex-wrap gap-2 text-sm text-slate-500">

            @if ($course->subject)
                <span>
                    {{ $course->subject->name }}
                </span>
            @endif

            @if ($course->schoolClass)
                <span>•</span>

                <span>
                    {{ $course->schoolClass->name }}
                </span>
            @endif

        </div>

        @if ($course->description)

            <p class="mt-4 max-w-3xl text-slate-500">
                {{ $course->description }}
            </p>

        @endif

    </div>


    <section>

        <div class="mb-4">

            <h2 class="text-xl font-semibold text-slate-900">
                Resources
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Learning materials for this course.
            </p>

        </div>


        @if ($course->resources->isEmpty())

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">

                <p class="font-medium text-slate-700">
                    No resources yet
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Your teacher has not added any resources.
                </p>

            </div>

        @else

            <div class="space-y-3">

                @foreach ($course->resources as $resource)

                    <a
                        href="{{ route('student.resources.show', $resource) }}"
                        class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md"
                    >

                        <div class="flex items-center justify-between gap-4">

                            <div>

                                <h3 class="font-semibold text-slate-900">
                                    {{ $resource->title }}
                                </h3>

                                @if ($resource->description)

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $resource->description }}
                                    </p>

                                @endif

                            </div>

                            <span class="text-sm font-medium text-slate-600">
                                Open →
                            </span>

                        </div>

                    </a>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection
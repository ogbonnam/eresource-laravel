@extends('layouts.dashboard')

@section('title', $pastPaper->title)

@section('content')

<div class="mx-auto max-w-5xl space-y-8">

    {{-- Header --}}
    <div>
        <a
            href="{{ route('student.practice.index') }}"
            class="text-sm font-medium text-primary-600 hover:underline"
        >
            ← Back to Practice Papers
        </a>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $pastPaper->subject?->name ?? 'Subject' }}
            </p>

            <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $pastPaper->title }}
            </h1>
        </div>
    </div>

    {{-- Paper information --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <div class="grid gap-6 sm:grid-cols-3">

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                    Exam
                </p>

                <p class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pastPaper->exam_type ?: 'Past Paper' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                    Year
                </p>

                <p class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pastPaper->exam_year ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                    Questions
                </p>

                <p class="mt-1 font-medium text-gray-900 dark:text-white">
                    {{ $pastPaper->questions->where('status', 'approved')->count() }}
                </p>
            </div>

        </div>

    </div>

    {{-- Exercises --}}
    <div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Exercises
        </h2>

        <div class="mt-4 space-y-3">

            @foreach ($pastPaper->questionSections as $section)

                @php
                    $questionCount = $section->questions_count;
                @endphp

                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">

                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ $section->title }}
                            </h3>

                            @if ($section->instructions)
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $section->instructions }}
                                </p>
                            @endif
                        </div>

                        <span class="shrink-0 text-sm text-gray-500">
                            {{ $questionCount }}
                            {{ \Illuminate\Support\Str::plural('question', $questionCount) }}
                        </span>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

    {{-- Start button --}}
    <div class="flex justify-end">

        <a
            href="{{ route('student.practice.start', $pastPaper) }}"
            class="inline-flex items-center rounded-lg bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700"
        >
            Start Practice
            <span class="ml-2">→</span>
        </a>

    </div>

</div>

@endsection
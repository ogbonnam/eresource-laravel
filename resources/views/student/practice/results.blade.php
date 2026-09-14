@extends('layouts.dashboard')

@section('title', 'Practice Results')

@section('content')

<div class="mx-auto max-w-5xl space-y-8">

{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div>
    <a
        href="{{ route('student.practice.index') }}"
        class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400"
    >
        ← Back to Practice Papers
    </a>

    <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
        Practice Results
    </h1>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        {{ $pastPaper->title }}
    </p>
</div>


{{-- ========================================================= --}}
{{-- SCORE SUMMARY --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 gap-5 md:grid-cols-3">

    {{-- Automatic Score --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Automatic Score
        </p>

        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
            {{ $result['automatic_marks'] }}
            /
            {{ $result['total_marks'] - $result['manual_marks'] }}
        </p>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Automatically marked questions
        </p>

    </div>


    {{-- Manual Marking --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Manual Marking
        </p>

        @if (($result['manual_marks'] ?? 0) > 0)

            <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">
                {{ $result['manual_marks'] }} marks
            </p>

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Awaiting teacher marking
            </p>

        @else

            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">
                0 marks
            </p>

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                No manual marking required
            </p>

        @endif

    </div>


    {{-- Total Paper Marks --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Total Paper Marks
        </p>

        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
            {{ $result['total_marks'] }}
        </p>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Including questions awaiting marking
        </p>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MANUAL MARKING NOTICE --}}
{{-- ========================================================= --}}

@if (($result['manual_marks'] ?? 0) > 0)

    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900 dark:bg-amber-950/30">

        <div class="flex gap-3">

            <div class="mt-0.5 text-amber-600 dark:text-amber-400">
                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3.14h15.64a2 2 0 001.71-3.14l-7.82-13a2 2 0 00-1.71 0z"
                    />
                </svg>
            </div>

            <div>

                <p class="font-semibold text-amber-900 dark:text-amber-200">
                    Some questions require teacher marking
                </p>

                <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                    Your automatic score does not yet include
                    {{ $result['manual_marks'] }} mark(s) from writing or essay questions.
                </p>

            </div>

        </div>

    </div>

@endif


{{-- ========================================================= --}}
{{-- QUESTION REVIEW --}}
{{-- ========================================================= --}}

<div>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
        Question Review
    </h2>

    <div class="mt-5 space-y-5">

        @foreach ($result['question_results'] as $index => $questionResult)

            @php

                $marksAwarded = (int) ($questionResult['marks_awarded'] ?? 0);

                $marksAvailable = (int) ($questionResult['marks_available'] ?? 0);

                $status = $questionResult['status'] ?? 'incorrect';

                $isCorrect = $status === 'correct';

                $isPartial = $status === 'partial';

                $isManual = $status === 'manual';

            @endphp


            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

                {{-- Question Header --}}
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                        <div>

                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                Question {{ $index + 1 }}
                            </p>

                            <h3 class="mt-1 font-semibold text-gray-900 dark:text-white">
                                {{ $questionResult['question'] }}
                            </h3>

                        </div>


                        {{-- Status --}}
                        <div class="shrink-0">

                            @if ($isCorrect)

                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                    Correct
                                </span>

                            @elseif ($isPartial)

                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    Partial Credit
                                </span>

                            @elseif ($isManual)

                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                    Manual Marking
                                </span>

                            @else

                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                    Incorrect
                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- Question Body --}}
                <div class="space-y-5 p-5">

                    {{-- Student Answer --}}
                    <div>

                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Your answer
                        </p>

                        <div class="mt-2 rounded-lg bg-gray-50 p-4 text-sm text-gray-800 dark:bg-gray-900 dark:text-gray-200">

                            @if (
                                isset($questionResult['student_answer'])
                                && $questionResult['student_answer'] !== null
                                && trim((string) $questionResult['student_answer']) !== ''
                            )

                                {{ $questionResult['student_answer'] }}

                            @else

                                <span class="italic text-gray-500">
                                    No answer submitted
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- Correct Answer --}}
                    @if (!$isManual)

                        <div>

                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Correct answer
                            </p>

                            <div class="mt-2 rounded-lg bg-green-50 p-4 text-sm text-green-900 dark:bg-green-950/30 dark:text-green-200">

                                {{ $questionResult['correct_answer'] ?? '—' }}

                            </div>

                        </div>

                    @endif


                    {{-- Marking Points --}}
                    @if (
                        ($questionResult['marking_method'] ?? null) === 'marking_points'
                        && isset($questionResult['matched_points'])
                        && isset($questionResult['total_marking_points'])
                    )

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">

                            <div class="flex items-center justify-between gap-4">

                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Marking points matched
                                </p>

                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $questionResult['matched_points'] }}
                                    /
                                    {{ $questionResult['total_marking_points'] }}
                                </span>

                            </div>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Credit is awarded for each relevant marking point identified in your answer.
                            </p>

                        </div>

                    @endif


                    {{-- Marks --}}
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">

                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Marks
                        </span>

                        <span
                            class="
                                text-lg font-bold
                                @if ($isCorrect)
                                    text-green-600 dark:text-green-400
                                @elseif ($isPartial)
                                    text-amber-600 dark:text-amber-400
                                @elseif ($isManual)
                                    text-blue-600 dark:text-blue-400
                                @else
                                    text-red-600 dark:text-red-400
                                @endif
                            "
                        >
                            {{ $marksAwarded }}
                            /
                            {{ $marksAvailable }}
                        </span>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>


{{-- ========================================================= --}}
{{-- ACTIONS --}}
{{-- ========================================================= --}}

<div class="flex flex-col gap-3 sm:flex-row">

    <a
        href="{{ route('student.practice.index') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
    >
        Back to Practice Papers
    </a>

    <a
        href="{{ route('student.practice.show', $pastPaper) }}"
        class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700"
    >
        Practice Again
    </a>

</div>


</div>

@endsection

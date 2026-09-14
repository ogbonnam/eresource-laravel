@extends('layouts.dashboard')

@section('title', $pastPaper->title . ' · Practice')

@section('content')

<div class="mx-auto max-w-7xl">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <a
                href="{{ route('student.practice.show', $pastPaper) }}"
                class="text-sm font-medium text-primary-600 hover:underline"
            >
                ← Exit Practice
            </a>

            <h1 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">
                {{ $pastPaper->title }}
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $pastPaper->subject?->name ?? 'Subject' }}

                @if ($pastPaper->exam_year)
                    · {{ $pastPaper->exam_year }}
                @endif
            </p>

        </div>


        {{-- Progress --}}

        @php
            $totalQuestions = $questions->count();
            $currentNumber = $currentIndex + 1;
            $progress = $totalQuestions > 0
                ? ($currentNumber / $totalQuestions) * 100
                : 0;
        @endphp

        <div class="text-left sm:text-right">

            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Question {{ $currentNumber }} of {{ $totalQuestions }}
            </p>

            <div class="mt-2 h-2 w-48 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">

                <div
                    class="h-full rounded-full bg-primary-600 transition-all duration-300"
                    style="width: {{ $progress }}%"
                ></div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MAIN LAYOUT
    ========================================================== --}}

    <div class="grid gap-6 lg:grid-cols-2">


        {{-- =====================================================
             READING MATERIAL
        ====================================================== --}}

        <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >

            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">

                <div class="flex items-center justify-between gap-4">

                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Reading Material
                    </h2>

                    @if ($currentQuestion->section)

                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ $currentQuestion->section->title }}
                        </span>

                    @endif

                </div>

            </div>


            <div class="max-h-[70vh] overflow-y-auto p-6">

                @if ($currentQuestion->section?->instructions)

                    <div class="mb-5 rounded-lg bg-primary-50 p-4 text-sm text-primary-900 dark:bg-primary-950 dark:text-primary-100">

                        <p class="font-semibold">
                            Instructions
                        </p>

                        <p class="mt-1 whitespace-pre-line">
                            {{ $currentQuestion->section->instructions }}
                        </p>

                    </div>

                @endif


                @if ($currentQuestion->section?->stimulus)

                    <div class="prose prose-sm max-w-none dark:prose-invert">

                        {!! nl2br(e($currentQuestion->section->stimulus)) !!}

                    </div>

                @else

                    <div class="rounded-lg bg-gray-50 p-5 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-400">

                        This question does not have a separate reading passage.

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             QUESTION
        ====================================================== --}}

        <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >

            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">

                <div class="flex items-center justify-between gap-4">

                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Question
                    </h2>

                    <span class="text-sm text-gray-500 dark:text-gray-400">

                        {{ $currentQuestion->marks }}

                        {{ \Illuminate\Support\Str::plural('mark', $currentQuestion->marks) }}

                    </span>

                </div>

            </div>


            <div class="p-6">

                {{-- Question classification --}}

                <div class="mb-5 flex flex-wrap gap-2">

                    @if ($currentQuestion->topic)

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            {{ $currentQuestion->topic }}
                        </span>

                    @endif

                    @if ($currentQuestion->difficulty)

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            {{ ucfirst($currentQuestion->difficulty) }}
                        </span>

                    @endif

                </div>


                {{-- Question text --}}

                <div class="text-base leading-7 text-gray-900 dark:text-white">

                    {!! nl2br(e($currentQuestion->question)) !!}

                </div>


                {{-- =================================================
                     ANSWER FORM
                ================================================== --}}

                @php
                    $savedAnswer = $answers[(string) $currentQuestion->id] ?? null;
                @endphp

                <form
                    method="POST"
                    action="{{ route('student.practice.answer', [
                        'pastPaper' => $pastPaper,
                        'question' => $currentQuestion,
                    ]) }}"
                    class="mt-8"
                >

                    @csrf


                    {{-- ===============================
                         MULTIPLE CHOICE
                    ================================ --}}

                    @if ($currentQuestion->question_type === 'multiple_choice')

                        <div class="space-y-3">

                            @foreach ($currentQuestion->options as $optionIndex => $option)

                                @php
                                    $displayLabel = chr(65 + $optionIndex);
                                @endphp

                                <label
                                    class="flex cursor-pointer items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 transition hover:border-primary-400 hover:bg-primary-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-primary-500 dark:hover:bg-primary-950"
                                >

                                    <input
                                        type="radio"
                                        name="answer"
                                        value="{{ $option->id }}"
                                        @checked((string) $savedAnswer === (string) $option->id)
                                        class="mt-1 h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500"
                                    >

                                    <div>

                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $displayLabel }}.
                                        </span>

                                        <span class="ml-1 text-gray-700 dark:text-gray-300">
                                            {{ $option->option_text }}
                                        </span>

                                    </div>

                                </label>

                            @endforeach

                        </div>


                    {{-- ===============================
                         SHORT ANSWER
                    ================================ --}}

                    @elseif ($currentQuestion->question_type === 'short_answer')

                        <div>

                            <label
                                for="answer"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Your answer
                            </label>

                            <input
                                type="text"
                                id="answer"
                                name="answer"
                                value="{{ old('answer', $savedAnswer) }}"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                placeholder="Type your answer..."
                            >

                        </div>


                    {{-- ===============================
                         LONG ANSWER / WRITING
                    ================================ --}}

                    @else

                        <div>

                            <label
                                for="answer"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Your answer
                            </label>

                            <textarea
                                id="answer"
                                name="answer"
                                rows="12"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                placeholder="Write your answer here..."
                            >{{ old('answer', $savedAnswer) }}</textarea>

                        </div>

                    @endif


                    {{-- =================================================
                         NAVIGATION
                    ================================================== --}}

                    <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6 dark:border-gray-700">


                        {{-- Previous --}}

                        @if ($currentIndex > 0)

                            <button
                                type="submit"
                                name="direction"
                                value="previous"
                                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                ← Previous
                            </button>

                        @else

                            <div></div>

                        @endif


                        {{-- Next / Finish --}}

                        @if ($currentIndex < $totalQuestions - 1)

                            <button
                                type="submit"
                                name="direction"
                                value="next"
                                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Next →
                            </button>

                        @else

                            <button
                                type="submit"
                                formaction="{{ route('student.practice.submit', $pastPaper) }}"
                                formmethod="POST"
                                class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            >
                                Finish Practice
                            </button>

                        @endif

                    </div>

                </form>

            </div>

        </section>

    </div>

</div>

@endsection
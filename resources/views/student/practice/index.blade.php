@extends('layouts.dashboard')

@section('title', 'Practice Papers')

@section('content')

<div class="mx-auto max-w-7xl space-y-8">

    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Practice Papers
        </h1>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Practise using past examination papers and approved questions.
        </p>
    </div>

    @if ($pastPapers->isEmpty())

        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                No practice papers available
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Approved practice papers will appear here when they are available.
            </p>
        </div>

    @else

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

            @foreach ($pastPapers as $pastPaper)

                <a
                    href="{{ route('student.practice.show', $pastPaper) }}"
                    class="group rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >

                    <div class="flex items-start justify-between gap-4">

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $pastPaper->subject?->name ?? 'Subject' }}
                            </p>

                            <h2 class="mt-1 text-lg font-semibold text-gray-900 group-hover:text-primary-600 dark:text-white">
                                {{ $pastPaper->title }}
                            </h2>
                        </div>

                        @if ($pastPaper->exam_year)
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                {{ $pastPaper->exam_year }}
                            </span>
                        @endif

                    </div>

                    <div class="mt-5 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">

                        @if ($pastPaper->exam_type)
                            <span>
                                {{ $pastPaper->exam_type }}
                            </span>
                        @endif

                        <span>
                            {{ $pastPaper->questions_count }} approved
                            {{ \Illuminate\Support\Str::plural('question', $pastPaper->questions_count) }}
                        </span>

                    </div>

                    <div class="mt-6 text-sm font-medium text-primary-600">
                        View paper →
                    </div>

                </a>

            @endforeach

        </div>

    @endif

</div>

@endsection
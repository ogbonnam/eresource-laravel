@extends('layouts.dashboard')

@section('title', 'Review Lesson Plan')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="mx-auto max-w-5xl space-y-8">

    <div>

        <a
            href="{{ route('teacher.lesson-plan-vetting.index') }}"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
        >
            ← Back to Vetting
        </a>

        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

            <div>

                <h1 class="text-2xl font-bold text-gray-900">
                    Review Lesson Plan
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Review the submitted lesson plan before making a decision.
                </p>

            </div>

            <span class="inline-flex w-fit rounded-full bg-yellow-100 px-3 py-1.5 text-xs font-semibold text-yellow-800">
                Pending Vetting
            </span>

        </div>

    </div>

    {{-- Teacher / Course information --}}

    <div class="grid gap-6 md:grid-cols-2">

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                Teacher
            </h2>

            <p class="mt-2 text-lg font-semibold text-gray-900">
                {{ $lessonPlan->teacher->name }}
            </p>

            <p class="mt-1 text-sm text-gray-500">
                {{ $lessonPlan->teacher->email }}
            </p>

            <p class="mt-3 text-sm text-indigo-600">
                {{ $lessonPlan->faculty->name }}
            </p>

        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                Class
            </h2>

            <p class="mt-2 text-lg font-semibold text-gray-900">
                {{ $lessonPlan->schoolClass->name ?? '—' }}
            </p>
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                Subject
            </h2>

            <p class="mt-2 text-lg font-semibold text-gray-900">
                {{ $lessonPlan->subject->name ?? '—' }}
            </p>
        </div>

    </div>

    {{-- Lesson details --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <div class="grid gap-6 md:grid-cols-3">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Topic
                </p>

                <p class="mt-1 font-semibold text-gray-900">
                    {{ $lessonPlan->topic }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Week
                </p>

                <p class="mt-1 font-semibold text-gray-900">
                    {{ $lessonPlan->week ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Lesson Date
                </p>

                <p class="mt-1 font-semibold text-gray-900">
                    {{ $lessonPlan->lesson_date?->format('d M Y') ?? '—' }}
                </p>
            </div>

        </div>

    </div>

    {{-- Objectives --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-gray-900">
            Learning Objectives
        </h2>

        <div class="prose prose-sm mt-4 max-w-none text-gray-700">
            {!! nl2br(e($lessonPlan->objectives ?? 'No objectives provided.')) !!}
        </div>

    </div>

    {{-- Activities --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-gray-900">
            Teaching & Learning Activities
        </h2>

        <div class="prose prose-sm mt-4 max-w-none text-gray-700">
            {!! nl2br(e($lessonPlan->activities ?? 'No activities provided.')) !!}
        </div>

    </div>

    {{-- Assessment --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-gray-900">
            Assessment
        </h2>

        <div class="prose prose-sm mt-4 max-w-none text-gray-700">
            {!! nl2br(e($lessonPlan->assessment ?? 'No assessment provided.')) !!}
        </div>

    </div>

    {{-- Teacher comment --}}

    @if($lessonPlan->teacher_comment)

        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-6">

            <h2 class="font-semibold text-indigo-900">
                Teacher's Comment
            </h2>

            <p class="mt-3 whitespace-pre-line text-sm text-indigo-800">
                {{ $lessonPlan->teacher_comment }}
            </p>

        </div>

    @endif

    {{-- Supporting document --}}

    @if($lessonPlan->file_path)

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Supporting Document
            </h2>

            <a
                href="{{ Storage::disk('public')->url($lessonPlan->file_path) }}"
                target="_blank"
                class="mt-4 inline-flex rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
                Open Lesson Plan Document
            </a>

        </div>

    @endif

    {{-- Decision section --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-gray-900">
            Vetting Decision
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Choose an action and provide feedback where required.
        </p>

        <div class="mt-6 space-y-6">

            {{-- Approve --}}

            <form
                method="POST"
                action="{{ route('teacher.lesson-plan-vetting.approve', $lessonPlan) }}"
                class="rounded-lg border border-green-200 bg-green-50 p-5"
            >

                @csrf

                <h3 class="font-semibold text-green-900">
                    Approve Lesson Plan
                </h3>

                <textarea
                    name="vetter_comment"
                    rows="3"
                    placeholder="Optional approval comment"
                    class="mt-3 block w-full rounded-lg border-green-300 bg-white text-sm focus:border-green-500 focus:ring-green-500"
                ></textarea>

                <button
                    type="submit"
                    class="mt-3 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                >
                    Approve
                </button>

            </form>

            {{-- Revision --}}

            <form
                method="POST"
                action="{{ route('teacher.lesson-plan-vetting.revision', $lessonPlan) }}"
                class="rounded-lg border border-blue-200 bg-blue-50 p-5"
            >

                @csrf

                <h3 class="font-semibold text-blue-900">
                    Request Revision
                </h3>

                <p class="mt-1 text-sm text-blue-700">
                    Explain clearly what the teacher needs to correct.
                </p>

                <textarea
                    name="vetter_comment"
                    rows="4"
                    required
                    placeholder="Explain the changes required..."
                    class="mt-3 block w-full rounded-lg border-blue-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500"
                ></textarea>

                <button
                    type="submit"
                    class="mt-3 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Request Revision
                </button>

            </form>

            {{-- Reject --}}

            <form
                method="POST"
                action="{{ route('teacher.lesson-plan-vetting.reject', $lessonPlan) }}"
                class="rounded-lg border border-red-200 bg-red-50 p-5"
            >

                @csrf

                <h3 class="font-semibold text-red-900">
                    Reject Lesson Plan
                </h3>

                <textarea
                    name="vetter_comment"
                    rows="4"
                    required
                    placeholder="Explain why this lesson plan is being rejected..."
                    class="mt-3 block w-full rounded-lg border-red-300 bg-white text-sm focus:border-red-500 focus:ring-red-500"
                ></textarea>

                <button
                    type="submit"
                    class="mt-3 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                >
                    Reject
                </button>

            </form>

        </div>

    </div>

</div>

@endsection
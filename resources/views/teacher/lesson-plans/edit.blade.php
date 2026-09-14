@extends('layouts.dashboard')

@section('title', 'Edit Lesson Plan')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="mx-auto max-w-5xl space-y-8">

    <div>
        <a
            href="{{ route('teacher.lesson-plans.index') }}"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
        >
            ← Back to Lesson Plans
        </a>

        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Lesson Plan
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Update your lesson plan before submitting it for vetting.
                </p>
            </div>

            @if($lessonPlan->status === 'revision_requested')

                <span class="inline-flex w-fit rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-800">
                    Revision Requested
                </span>

            @elseif($lessonPlan->status === 'draft')

                <span class="inline-flex w-fit rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">
                    Draft
                </span>

            @endif

        </div>
    </div>

    @if($lessonPlan->status === 'revision_requested' && $lessonPlan->vetter_comment)

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">

            <h2 class="font-semibold text-blue-900">
                Feedback from HOD/HOF
            </h2>

            <p class="mt-2 whitespace-pre-line text-sm text-blue-800">
                {{ $lessonPlan->vetter_comment }}
            </p>

            @if($lessonPlan->vetter)
                <p class="mt-3 text-xs text-blue-700">
                    — {{ $lessonPlan->vetter->name }}
                </p>
            @endif

        </div>

    @endif

    @if($errors->any())

        <div class="rounded-lg border border-red-200 bg-red-50 p-4">

            <p class="font-semibold text-red-800">
                Please correct the following:
            </p>

            <ul class="mt-2 list-inside list-disc text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('teacher.lesson-plans.update', $lessonPlan) }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @method('PUT')

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Lesson Information
            </h2>

            <div class="mt-6 grid gap-6 md:grid-cols-2">

                <div>
                    <label
                        for="class_id"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Class <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="class_id"
                        name="class_id"
                        required
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            Select class
                        </option>

                        @foreach($classes as $class)
                            <option
                                value="{{ $class->id }}"
                                @selected(old('class_id', $lessonPlan->class_id) == $class->id)
                            >
                                {{ $class->name }}

                                @if($class->code)
                                    — {{ $class->code }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="subject_id"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Subject <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="subject_id"
                        name="subject_id"
                        required
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            Select subject
                        </option>

                        @foreach($subjects as $subject)
                            <option
                                value="{{ $subject->id }}"
                                @selected(old('subject_id', $lessonPlan->subject_id) == $subject->id)
                            >
                                {{ $subject->name }}

                                @if($subject->code)
                                    — {{ $subject->code }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>

                    <label
                        for="week"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Week
                    </label>

                    <input
                        id="week"
                        type="number"
                        name="week"
                        min="1"
                        max="52"
                        value="{{ old('week', $lessonPlan->week) }}"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                </div>

                <div>

                    <label
                        for="lesson_date"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Lesson Date
                    </label>

                    <input
                        id="lesson_date"
                        type="date"
                        name="lesson_date"
                        value="{{ old('lesson_date', $lessonPlan->lesson_date?->format('Y-m-d')) }}"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                </div>

                <div class="md:col-span-2">

                    <label
                        for="topic"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Topic <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="topic"
                        type="text"
                        name="topic"
                        value="{{ old('topic', $lessonPlan->topic) }}"
                        required
                        maxlength="255"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                </div>

            </div>

        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Lesson Content
            </h2>

            <div class="mt-6 space-y-6">

                <div>

                    <label
                        for="objectives"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Learning Objectives
                    </label>

                    <textarea
                        id="objectives"
                        name="objectives"
                        rows="5"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('objectives', $lessonPlan->objectives) }}</textarea>

                </div>

                <div>

                    <label
                        for="activities"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Teaching & Learning Activities
                    </label>

                    <textarea
                        id="activities"
                        name="activities"
                        rows="7"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('activities', $lessonPlan->activities) }}</textarea>

                </div>

                <div>

                    <label
                        for="assessment"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Assessment
                    </label>

                    <textarea
                        id="assessment"
                        name="assessment"
                        rows="5"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('assessment', $lessonPlan->assessment) }}</textarea>

                </div>

            </div>

        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Supporting Document
            </h2>

            @if($lessonPlan->file_path)

                <div class="mt-4 rounded-lg bg-gray-50 p-4">

                    <p class="text-sm font-medium text-gray-700">
                        Current document
                    </p>

                    <a
                        href="{{ Storage::disk('public')->url($lessonPlan->file_path) }}"
                        target="_blank"
                        class="mt-1 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-700"
                    >
                        View current document →
                    </a>

                </div>

            @endif

            <div class="mt-5">

                <label
                    for="file_path"
                    class="block text-sm font-medium text-gray-700"
                >
                    Replace Document
                </label>

                <input
                    id="file_path"
                    type="file"
                    name="file_path"
                    accept=".pdf,.doc,.docx"
                    class="mt-2 block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-gray-50 file:px-4 file:py-2.5 file:text-sm file:font-medium"
                >

                <p class="mt-2 text-xs text-gray-500">
                    Leave empty to keep the current document.
                </p>

            </div>

            <div class="mt-6">

                <label
                    for="teacher_comment"
                    class="block text-sm font-medium text-gray-700"
                >
                    Comment to HOD/HOF
                </label>

                <textarea
                    id="teacher_comment"
                    name="teacher_comment"
                    rows="4"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('teacher_comment', $lessonPlan->teacher_comment) }}</textarea>

            </div>

        </div>

        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('teacher.lesson-plans.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
            >
                Save Changes
            </button>

        </div>

    </form>

</div>

@endsection
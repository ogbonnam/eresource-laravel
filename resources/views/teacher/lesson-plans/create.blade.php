@extends('layouts.dashboard')

@section('title', 'Create Lesson Plan')

@section('content')

<div class="mx-auto max-w-5xl space-y-8">


<div>

    <a
        href="{{ route('teacher.lesson-plans.index') }}"
        class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
    >
        ← Back to Lesson Plans
    </a>

    <h1 class="mt-3 text-2xl font-bold text-gray-900">
        Create Lesson Plan
    </h1>

    <p class="mt-1 text-sm text-gray-600">
        Prepare your lesson plan and save it as a draft. You can submit it for
        HOD/HOF vetting when you are ready.
    </p>

</div>

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

@if(!auth()->user()->faculty_id)

    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">

        You have not been assigned to a faculty yet.

        Please contact the administrator before creating a lesson plan.

    </div>

@endif

<form
    method="POST"
    action="{{ route('teacher.lesson-plans.store') }}"
    enctype="multipart/form-data"
    class="space-y-6"
>

    @csrf

    {{-- ========================================================= --}}
    {{-- LESSON INFORMATION --}}
    {{-- ========================================================= --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-gray-900">
            Lesson Information
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Select the class and subject this lesson plan is for.
        </p>

        <div class="mt-6 grid gap-6 md:grid-cols-2">

            {{-- CLASS --}}
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
                            @selected(old('class_id') == $class->id)
                        >
                            {{ $class->name }}

                            @if($class->code)
                                — {{ $class->code }}
                            @endif
                        </option>

                    @endforeach

                </select>

                @if($classes->isEmpty())

                    <p class="mt-2 text-sm text-red-600">
                        You currently have no classes assigned to you.
                    </p>

                @endif

            </div>

            {{-- SUBJECT --}}
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
                            @selected(old('subject_id') == $subject->id)
                        >
                            {{ $subject->name }}

                            @if($subject->code)
                                — {{ $subject->code }}
                            @endif
                        </option>

                    @endforeach

                </select>

                @if($subjects->isEmpty())

                    <p class="mt-2 text-sm text-red-600">
                        You currently have no subjects assigned to you.
                    </p>

                @endif

            </div>

            {{-- WEEK --}}
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
                    value="{{ old('week') }}"
                    placeholder="e.g. 3"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >

            </div>

            {{-- LESSON DATE --}}
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
                    value="{{ old('lesson_date') }}"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >

            </div>

            {{-- TOPIC --}}
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
                    value="{{ old('topic') }}"
                    required
                    maxlength="255"
                    placeholder="e.g. Cell Structure and Organisation"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- LESSON CONTENT --}}
    {{-- ========================================================= --}}

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
                    placeholder="What should students know or be able to do by the end of the lesson?"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('objectives') }}</textarea>

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
                    placeholder="Describe the teaching activities, student activities, practical work, discussion, group work, etc."
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('activities') }}</textarea>

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
                    placeholder="How will student understanding be assessed?"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('assessment') }}</textarea>

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- SUPPORTING DOCUMENT --}}
    {{-- ========================================================= --}}

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-semibold text-gray-900">
            Supporting Document
        </h2>

        <div class="mt-5">

            <label
                for="file_path"
                class="block text-sm font-medium text-gray-700"
            >
                Upload Lesson Plan
            </label>

            <input
                id="file_path"
                type="file"
                name="file_path"
                accept=".pdf,.doc,.docx"
                class="mt-2 block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-4 file:border-0 file:bg-gray-50 file:px-4 file:py-2.5 file:text-sm file:font-medium"
            >

            <p class="mt-2 text-xs text-gray-500">
                PDF, DOC or DOCX. Maximum size: 10 MB.
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
                placeholder="Optional message for the person reviewing this lesson plan."
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >{{ old('teacher_comment') }}</textarea>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- ACTIONS --}}
    {{-- ========================================================= --}}

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
            Save Draft
        </button>

    </div>

</form>

</div>

@endsection

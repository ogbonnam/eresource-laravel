@extends('layouts.dashboard')

@section('title', 'Create Course')

@section('content')

<div class="mx-auto max-w-3xl">

```
{{-- Header --}}
<div class="mb-8">

    <a
        href="{{ route('teacher.courses.index') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-4 w-4"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
            />
        </svg>

        Back to courses
    </a>

    <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-900">
        Create a course
    </h1>

    <p class="mt-2 text-slate-500">
        Select a class first, then choose a subject available for that class.
    </p>

</div>


{{-- Form --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

    <form
        method="POST"
        action="{{ route('teacher.courses.store') }}"
        class="space-y-6 p-6 sm:p-8"
    >

        @csrf


        {{-- Course Name --}}
        <div>

            <label
                for="name"
                class="block text-sm font-medium text-slate-700"
            >
                Course name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="e.g. Mathematics"
                required
                class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
            >

            @error('name')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Class --}}
        <div>

            <label
                for="class_id"
                class="block text-sm font-medium text-slate-700"
            >
                Class
            </label>

            <select
                id="class_id"
                name="class_id"
                required
                class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
            >

                <option value="">
                    Select class
                </option>

                @foreach ($classes as $class)

                    <option
                        value="{{ $class->id }}"
                        @selected((string) old('class_id') === (string) $class->id)
                    >
                        {{ $class->name }}
                    </option>

                @endforeach

            </select>

            @error('class_id')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Subject --}}
        <div>

            <label
                for="subject_id"
                class="block text-sm font-medium text-slate-700"
            >
                Subject
            </label>

            <select
                id="subject_id"
                name="subject_id"
                required
                disabled
                class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition disabled:bg-slate-100 disabled:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
            >

                <option value="">
                    Select a class first
                </option>

            </select>

            <p
                id="subject-help"
                class="mt-2 text-sm text-slate-500"
            >
                Select a class to see the subjects available for that class.
            </p>

            @error('subject_id')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Description --}}
        <div>

            <label
                for="description"
                class="block text-sm font-medium text-slate-700"
            >
                Description
                <span class="font-normal text-slate-400">
                    (optional)
                </span>
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="Describe what students will learn in this course..."
                class="mt-2 block w-full resize-none rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
            >{{ old('description') }}</textarea>

            @error('description')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- Information --}}
        <div class="rounded-xl bg-slate-50 p-4">

            <div class="flex gap-3">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="mt-0.5 h-5 w-5 shrink-0 text-slate-500"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11.25 11.25h.75v5.25h.75m-.75-9.75h.008v.008H12V6.75Z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                </svg>

                <div>

                    <p class="text-sm font-medium text-slate-700">
                        Class subjects
                    </p>

                    <p class="mt-1 text-sm text-slate-600">
                        Only subjects assigned to the selected class can be used for this course.
                    </p>

                </div>

            </div>

        </div>


        {{-- Actions --}}
        <div
            class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end"
        >

            <a
                href="{{ route('teacher.courses.index') }}"
                class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800"
            >
                Create course
            </button>

        </div>

    </form>

</div>
```

</div>

{{-- Class -> Subject JavaScript --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const subjectHelp = document.getElementById('subject-help');

    /*
    |--------------------------------------------------------------------------
    | Class -> Subjects
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | The controller already prepares this data.
    |
    | We DO NOT use:
    |
    | @json($classes->mapWithKeys(...))
    |
    | here anymore.
    |
    */

    const classSubjects = @json($classSubjects);

    const oldSubjectId = @json(old('subject_id'));


    /*
    |--------------------------------------------------------------------------
    | Update Subject Dropdown
    |--------------------------------------------------------------------------
    */

    function updateSubjects() {

        const classId = classSelect.value;

        /*
        | Reset subject dropdown.
        */

        subjectSelect.innerHTML = '';

        subjectSelect.disabled = true;


        /*
        | No class selected.
        */

        if (!classId) {

            const option = document.createElement('option');

            option.value = '';
            option.textContent = 'Select a class first';

            subjectSelect.appendChild(option);

            subjectHelp.textContent =
                'Select a class to see the subjects available for that class.';

            return;
        }


        /*
        | Get subjects for selected class.
        */

        const subjects = classSubjects[classId] || [];


        /*
        | Class has no subjects.
        */

        if (subjects.length === 0) {

            const option = document.createElement('option');

            option.value = '';
            option.textContent = 'No subjects available';

            subjectSelect.appendChild(option);

            subjectHelp.textContent =
                'No subjects have been assigned to this class yet. Please contact the administrator.';

            return;
        }


        /*
        | Add default option.
        */

        const defaultOption = document.createElement('option');

        defaultOption.value = '';
        defaultOption.textContent = 'Select subject';

        subjectSelect.appendChild(defaultOption);


        /*
        | Add available subjects.
        */

        subjects.forEach(function (subject) {

            const option = document.createElement('option');

            option.value = subject.id;
            option.textContent = subject.name;

            /*
            | Restore old subject after validation error.
            */

            if (
                oldSubjectId !== null &&
                String(oldSubjectId) === String(subject.id)
            ) {
                option.selected = true;
            }

            subjectSelect.appendChild(option);

        });


        /*
        | Enable subject dropdown.
        */

        subjectSelect.disabled = false;


        /*
        | Update help text.
        */

        subjectHelp.textContent =
            subjects.length +
            ' subject' +
            (subjects.length === 1 ? '' : 's') +
            ' available for this class.';

    }


    /*
    |--------------------------------------------------------------------------
    | Class Changed
    |--------------------------------------------------------------------------
    */

    classSelect.addEventListener('change', function () {

        updateSubjects();

    });


    /*
    |--------------------------------------------------------------------------
    | Load Existing Selection
    |--------------------------------------------------------------------------
    |
    | This is useful when Laravel redirects back because validation failed.
    |
    */

    updateSubjects();

});

</script>

@endsection

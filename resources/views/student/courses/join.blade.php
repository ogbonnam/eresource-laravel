
@extends('layouts.dashboard')

@section('title', 'Join a Course')

@section('content')

<div class="mx-auto max-w-3xl space-y-8">

    {{-- Header --}}
    <section>
        <p class="text-sm font-medium text-slate-500">Student</p>

        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">
            Join Your Subjects
        </h1>

        <p class="mt-2 max-w-xl text-slate-500">
            Join an individual course using your teacher's code, or use
            your year-group code to access all subjects available to your year.
        </p>
    </section>

    {{-- Success --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4">
            <p class="text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </p>
        </div>
    @endif

    {{-- General Error --}}
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">
            <p class="text-sm font-medium text-red-700">
                {{ session('error') }}
            </p>
        </div>
    @endif

    {{-- OPTION 1: Join All Subjects --}}
    <section class="overflow-hidden rounded-2xl border-2 border-indigo-200 bg-white shadow-sm">

        <div class="border-b border-indigo-100 bg-indigo-50 px-6 py-5">

            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor"
                         class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-semibold text-indigo-950">
                            Join All Subjects in Your Year
                        </h2>

                        <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                            Recommended
                        </span>
                    </div>

                    <p class="mt-1 text-sm leading-6 text-indigo-800">
                        Enter one year-group code to join all active subjects
                        across the classes assigned to your year group.
                    </p>
                </div>
            </div>

        </div>

        <form method="POST"
              action="{{ route('student.courses.join-year-group.store') }}"
              class="space-y-6 p-6 sm:p-8">

            @csrf

            <div>
                <label for="access_code"
                       class="block text-sm font-medium text-slate-700">
                    Year-group access code
                </label>

                <input
                    type="text"
                    id="access_code"
                    name="access_code"
                    value="{{ old('access_code') }}"
                    maxlength="30"
                    autocomplete="off"
                    autocapitalize="characters"
                    spellcheck="false"
                    placeholder="e.g. YEAR8K7M4P"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-4 text-center font-mono text-xl font-semibold uppercase tracking-[0.2em] text-slate-900 outline-none transition placeholder:font-sans placeholder:text-base placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                >

                @error('access_code')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-xs text-slate-400">
                    Ask your school administrator for your year-group code.
                </p>
            </div>

            <div class="rounded-xl bg-indigo-50 p-4">
                <p class="text-sm leading-6 text-indigo-900">
                    <strong>What happens next?</strong>
                    You will be enrolled in all active courses belonging
                    to your year group. Where approval is enabled,
                    access will be granted after the required approval.
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">

                    Join All Subjects

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor"
                         class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>

                </button>

            </div>

        </form>

    </section>


    {{-- OPTION 2: Existing Individual Course Enrollment --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor"
                         class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.75a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.5 12a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 12.75v3"/>
                    </svg>
                </div>

                <div>
                    <h2 class="font-semibold text-slate-900">
                        Join an Individual Course
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Already have a course-specific enrollment code?
                        Use it here.
                    </p>
                </div>
            </div>

        </div>

        <form method="POST"
              action="{{ route('student.courses.join.store') }}"
              class="space-y-6 p-6 sm:p-8">

            @csrf

            <div>
                <label for="enrollment_code"
                       class="block text-sm font-medium text-slate-700">
                    Course enrollment code
                </label>

                <input
                    type="text"
                    id="enrollment_code"
                    name="enrollment_code"
                    value="{{ old('enrollment_code') }}"
                    maxlength="20"
                    autocomplete="off"
                    autocapitalize="characters"
                    spellcheck="false"
                    placeholder="e.g. DAWITPHQ"
                    required
                    class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-4 text-center font-mono text-xl font-semibold uppercase tracking-[0.25em] text-slate-900 outline-none transition placeholder:font-sans placeholder:text-base placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
                >

                @error('enrollment_code')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-sm leading-6 text-slate-600">
                    Your teacher will receive your enrollment request.
                    You can access the course after the teacher approves it.
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800">

                    Join Course

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor"
                         class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>

                </button>

            </div>

        </form>

    </section>


    {{-- How it works --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="font-semibold text-slate-900">
            How enrollment works
        </h2>

        <div class="mt-5 space-y-5">

            <div class="flex gap-4">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                    1
                </div>

                <div>
                    <p class="font-medium text-slate-900">
                        Get your year-group code
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Your school administrator provides one code for
                        your year group.
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                    2
                </div>

                <div>
                    <p class="font-medium text-slate-900">
                        Enter the code
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Submit the code to find all active subjects
                        assigned to your year group.
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                    3
                </div>

                <div>
                    <p class="font-medium text-slate-900">
                        Access your subjects
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Your courses will appear on your student dashboard
                        according to the enrollment approval settings.
                    </p>
                </div>
            </div>

        </div>

    </section>

</div>

@endsection

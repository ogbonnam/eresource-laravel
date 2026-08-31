@extends('layouts.dashboard')

@section('title', 'Join Course')

@section('content')

<div class="mx-auto max-w-2xl space-y-8">

```
{{-- Header --}}
<section>

    <p class="text-sm font-medium text-slate-500">
        Student
    </p>

    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">
        Join a Course
    </h1>

    <p class="mt-2 max-w-xl text-slate-500">
        Enter the enrollment code provided by your teacher to request
        access to a course.
    </p>

</section>


{{-- Success --}}
@if (session('success'))

    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4">

        <div class="flex gap-3">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="currentColor"
                class="h-5 w-5 shrink-0 text-emerald-600"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75"
                />

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
            </svg>

            <p class="text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </p>

        </div>

    </div>

@endif


{{-- Error --}}
@if (session('error'))

    <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">

        <div class="flex gap-3">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="currentColor"
                class="h-5 w-5 shrink-0 text-red-600"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v3.75m0 3h.008v.008H12v-.008ZM10.615 3.891 2.91 17.25a1.875 1.875 0 0 0 1.624 2.813h14.932a1.875 1.875 0 0 0 1.624-2.813L13.385 3.891a1.59 1.59 0 0 0-2.77 0Z"
                />
            </svg>

            <p class="text-sm font-medium text-red-700">
                {{ session('error') }}
            </p>

        </div>

    </div>

@endif


{{-- Join Course Card --}}
<section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

    <div class="border-b border-slate-100 px-6 py-5">

        <h2 class="font-semibold text-slate-900">
            Enter Enrollment Code
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Your teacher should provide you with a course enrollment code.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('student.courses.join.store') }}"
        class="space-y-6 p-6 sm:p-8"
    >

        @csrf


        {{-- Enrollment Code --}}
        <div>

            <label
                for="enrollment_code"
                class="block text-sm font-medium text-slate-700"
            >
                Enrollment code
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

            <p class="mt-2 text-xs text-slate-400">
                Enter the code exactly as provided by your teacher.
            </p>

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

                <p class="text-sm leading-6 text-slate-600">
                    After you submit the code, your teacher will receive
                    your request. You will only gain access to the course
                    after the teacher approves your request.
                </p>

            </div>

        </div>


        {{-- Actions --}}
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800"
            >

                Join Course

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="h-4 w-4"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                    />
                </svg>

            </button>

        </div>

    </form>

</section>


{{-- How it works --}}
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

    <h2 class="font-semibold text-slate-900">
        How it works
    </h2>

    <div class="mt-5 space-y-5">

        <div class="flex gap-4">

            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                1
            </div>

            <div>

                <p class="font-medium text-slate-900">
                    Get the code
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Ask your teacher for the enrollment code for the course.
                </p>

            </div>

        </div>


        <div class="flex gap-4">

            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                2
            </div>

            <div>

                <p class="font-medium text-slate-900">
                    Submit your request
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Enter the code above and submit your request to join.
                </p>

            </div>

        </div>


        <div class="flex gap-4">

            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                3
            </div>

            <div>

                <p class="font-medium text-slate-900">
                    Wait for approval
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Your teacher must approve your request before you can
                    access the course resources.
                </p>

            </div>

        </div>

    </div>

</section>
```

</div>

@endsection

@extends('layouts.dashboard')

@section('title', 'Pending Enrollment Requests')

@section('content')

<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <section>

        <a
            href="{{ route('teacher.courses.students.index', $course) }}"
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

            Back to students
        </a>


        <div class="mt-6">

            <p class="text-sm font-medium text-slate-500">
                {{ $course->subject?->name ?? 'Course' }}
                ·
                {{ $course->schoolClass?->name ?? 'Class' }}
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                Pending Enrollment Requests
            </h1>

            <p class="mt-2 max-w-2xl text-slate-500">
                Review students who have requested to join
                <span class="font-medium text-slate-700">
                    {{ $course->name }}
                </span>.
            </p>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- Flash Messages --}}
    {{-- ========================================================= --}}

    @if (session('success'))

        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
            {{ session('success') }}
        </div>

    @endif


    @if (session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            {{ session('error') }}
        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- Pending Requests --}}
    {{-- ========================================================= --}}

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Section Header --}}

        <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-semibold text-slate-900">
                    Enrollment Requests
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $enrollments->count() }}
                    {{ Str::plural('student', $enrollments->count()) }}
                    waiting for approval.
                </p>

            </div>


            <a
                href="{{ route('teacher.courses.students.index', $course) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                View enrolled students
            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- Empty State --}}
        {{-- ===================================================== --}}

        @if ($enrollments->isEmpty())

            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-7 w-7 text-slate-500"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.125-.956 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.17-.786-3.09M15 19.128v.003c0 1.113-.285 2.17-.786 3.09M15 19.128a9.38 9.38 0 0 0-2.625.372 9.337 9.337 0 0 1-4.125-.956 4.125 4.125 0 0 1 7.533-2.493M9 19.128v-.003c0-1.113.285-2.17.786-3.09M9 19.128v.003c0 1.113-.285 2.17-.786 3.09M9 19.128a9.38 9.38 0 0 1-2.625.372 9.337 9.337 0 0 1-4.125-.956 4.125 4.125 0 0 1 7.533-2.493"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                        />

                    </svg>

                </div>


                <h3 class="mt-5 text-lg font-semibold text-slate-900">
                    No pending requests
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    There are currently no students waiting for approval.
                    When students join using your course enrollment code,
                    their requests will appear here.
                </p>

            </div>


        @else

            {{-- ================================================= --}}
            {{-- Desktop Table --}}
            {{-- ================================================= --}}

            <div class="hidden overflow-x-auto md:block">

                <table class="w-full">

                    <thead class="border-b border-slate-100 bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Student
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Email
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Requested
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach ($enrollments as $enrollment)

                            <tr class="transition hover:bg-slate-50">

                                {{-- Student --}}

                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">

                                            {{ strtoupper(substr($enrollment->user->name ?? 'S', 0, 1)) }}

                                        </div>


                                        <div>

                                            <p class="font-medium text-slate-900">
                                                {{ $enrollment->user->name ?? 'Unknown Student' }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Student
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Email --}}

                                <td class="px-6 py-5">

                                    <p class="text-sm text-slate-600">
                                        {{ $enrollment->user->email ?? 'No email' }}
                                    </p>

                                </td>


                                {{-- Requested --}}

                                <td class="px-6 py-5">

                                    <p class="text-sm text-slate-600">
                                        {{ $enrollment->created_at?->format('M d, Y') }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        {{ $enrollment->created_at?->format('g:i A') }}
                                    </p>

                                </td>


                                {{-- Actions --}}

                                <td class="px-6 py-5">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Reject --}}

                                        <form
                                            method="POST"
                                            action="{{ route('teacher.courses.students.reject', [$course, $enrollment]) }}"
                                            onsubmit="return confirm('Reject this enrollment request?')"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
                                            >
                                                Reject
                                            </button>

                                        </form>


                                        {{-- Approve --}}

                                        <form
                                            method="POST"
                                            action="{{ route('teacher.courses.students.approve', [$course, $enrollment]) }}"
                                            onsubmit="return confirm('Approve this student for the course?')"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                                            >
                                                Approve
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- ================================================= --}}
            {{-- Mobile Cards --}}
            {{-- ================================================= --}}

            <div class="divide-y divide-slate-100 md:hidden">

                @foreach ($enrollments as $enrollment)

                    <div class="p-5">

                        {{-- Student Information --}}

                        <div class="flex items-start gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">

                                {{ strtoupper(substr($enrollment->user->name ?? 'S', 0, 1)) }}

                            </div>


                            <div class="min-w-0 flex-1">

                                <p class="font-medium text-slate-900">
                                    {{ $enrollment->user->name ?? 'Unknown Student' }}
                                </p>

                                <p class="mt-1 break-all text-sm text-slate-500">
                                    {{ $enrollment->user->email ?? 'No email' }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    Requested
                                    {{ $enrollment->created_at?->format('M d, Y g:i A') }}
                                </p>

                            </div>

                        </div>


                        {{-- Actions --}}

                        <div class="mt-5 grid grid-cols-2 gap-3">

                            {{-- Reject --}}

                            <form
                                method="POST"
                                action="{{ route('teacher.courses.students.reject', [$course, $enrollment]) }}"
                                onsubmit="return confirm('Reject this enrollment request?')"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="w-full rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
                                >
                                    Reject
                                </button>

                            </form>


                            {{-- Approve --}}

                            <form
                                method="POST"
                                action="{{ route('teacher.courses.students.approve', [$course, $enrollment]) }}"
                                onsubmit="return confirm('Approve this student for the course?')"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                                >
                                    Approve
                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection
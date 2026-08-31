@extends('layouts.dashboard')

@section('title', 'Add Student · ' . $course->name)

@section('content')

<div class="mx-auto max-w-3xl space-y-8">

    <section>

        <a
            href="{{ route('teacher.courses.students.index', $course) }}"
            class="text-sm font-medium text-slate-500 hover:text-slate-900"
        >
            ← Back to Students
        </a>

        <div class="mt-6">

            <p class="text-sm font-medium text-slate-500">
                {{ $course->name }}
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                Add Student
            </h1>

            <p class="mt-2 text-slate-500">
                Enroll a student in this course.
            </p>

        </div>

    </section>


    @if ($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 p-5">

            <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <h2 class="font-semibold text-slate-900">
                Select Student
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Only students who are not already enrolled are shown.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('teacher.courses.students.store', $course) }}"
            class="p-6"
        >

            @csrf


            @if ($students->isEmpty())

                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                    <p class="font-medium text-slate-700">
                        No students available
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        All available students are already enrolled in this course.
                    </p>

                </div>

            @else

                <div class="space-y-2">

                    @foreach ($students as $student)

                        <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-slate-400 hover:bg-slate-50">

                            <input
                                type="radio"
                                name="student_id"
                                value="{{ $student->id }}"
                                class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-slate-300"
                                @checked(old('student_id') == $student->id)
                            >

                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>

                            <div>

                                <p class="font-medium text-slate-900">
                                    {{ $student->name }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    {{ $student->email }}
                                </p>

                            </div>

                        </label>

                    @endforeach

                </div>


                <div class="mt-6 flex items-center justify-between">

                    <a
                        href="{{ route('teacher.courses.students.index', $course) }}"
                        class="rounded-xl px-5 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800"
                    >
                        Enroll Student
                    </button>

                </div>

            @endif

        </form>

    </section>

</div>

@endsection
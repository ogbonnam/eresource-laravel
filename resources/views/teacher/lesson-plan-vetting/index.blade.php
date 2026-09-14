@extends('layouts.dashboard')

@section('title', 'Lesson Plan Vetting')

@section('content')

<div class="mx-auto max-w-7xl space-y-8">

    <div>

        <a
            href="{{ url('/teacher') }}"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
        >
            ← Teacher Dashboard
        </a>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Lesson Plan Vetting
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Review lesson plans submitted by teachers in your faculty.
                </p>
            </div>

            <a
                href="{{ route('teacher.lesson-plan-vetting.approved') }}"
                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
            >
                Approved Lesson Plans
            </a>
        </div>

        @if(auth()->user()->faculty)
            <p class="mt-2 text-sm font-medium text-indigo-600">
                {{ auth()->user()->faculty->name }}
            </p>
        @endif

    </div>

    @if(session('success'))

        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>

    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Teacher
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Course
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Topic
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Date
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Submitted
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($lessonPlans as $lessonPlan)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">

                                <div class="font-medium text-gray-900">
                                    {{ $lessonPlan->teacher->name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $lessonPlan->teacher->email }}
                                </div>

                            </td>

                            <td class="px-6 py-4">

                                <div class="text-sm font-medium text-gray-900">
                                    {{ $lessonPlan->schoolClass->name ?? '—' }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $lessonPlan->subject->name ?? '—' }}
                                </div>

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $lessonPlan->topic }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $lessonPlan->lesson_date?->format('d M Y') ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $lessonPlan->submitted_at?->format('d M Y H:i') ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route('teacher.lesson-plan-vetting.show', $lessonPlan) }}"
                                    class="inline-flex rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                                >
                                    Review
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-6 py-12 text-center">

                                <div class="text-sm font-semibold text-gray-900">
                                    No lesson plans awaiting vetting
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Submitted lesson plans from your faculty will appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($lessonPlans->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $lessonPlans->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
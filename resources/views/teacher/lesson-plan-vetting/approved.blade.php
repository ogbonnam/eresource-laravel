@extends('layouts.dashboard')

@section('title', 'Approved Lesson Plans')

@section('content')

<div class="mx-auto max-w-7xl space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Approved Lesson Plans
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                All approved lesson plans submitted by teachers in your faculty.
            </p>
        </div>

        <a
            href="{{ route('teacher.lesson-plan-vetting.index') }}"
            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800"
        >
            Pending Vetting
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- APPROVED COUNT --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="text-sm text-gray-500">
            Approved Lesson Plans
        </div>

        <div class="mt-1 text-3xl font-bold text-green-600">
            {{ $lessonPlans->total() }}
        </div>
    </div>

    {{-- TABLE --}}
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
                            Lesson Date
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Approved By
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Approved
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse($lessonPlans as $lessonPlan)

                        <tr class="hover:bg-gray-50">

                            {{-- TEACHER --}}
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $lessonPlan->teacher->name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $lessonPlan->teacher->email }}
                                </div>
                            </td>

                            {{-- COURSE --}}
                            {{-- CLASS / SUBJECT --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $lessonPlan->schoolClass->name ?? '—' }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $lessonPlan->subject->name ?? '—' }}
                                </div>
                            </td>

                            {{-- TOPIC --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $lessonPlan->topic }}
                                </div>

                                @if($lessonPlan->week)
                                    <div class="text-xs text-gray-500">
                                        Week {{ $lessonPlan->week }}
                                    </div>
                                @endif
                            </td>

                            {{-- LESSON DATE --}}
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $lessonPlan->lesson_date?->format('d M Y') ?? '—' }}
                            </td>

                            {{-- APPROVED BY --}}
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $lessonPlan->vetter?->name ?? '—' }}
                            </td>

                            {{-- APPROVED DATE --}}
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $lessonPlan->approved_at?->format('d M Y H:i') ?? '—' }}
                            </td>

                            {{-- ACTION --}}
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a
                                    href="{{ route('teacher.lesson-plan-vetting.show', $lessonPlan) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    View
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-sm font-medium text-gray-900">
                                    No approved lesson plans yet.
                                </div>

                                <div class="mt-1 text-sm text-gray-500">
                                    Approved lesson plans from your faculty will appear here.
                                </div>
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
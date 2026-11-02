@extends('layouts.dashboard')

@section('title', 'Lesson Plans')

@section('content')

<div class="mx-auto max-w-7xl space-y-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Lesson Plans
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Create, submit and track your lesson plans.
            </p>
        </div>

        <a
            href="{{ route('teacher.lesson-plans.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
        >
            + Create Lesson Plan
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Course
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Topic
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Files
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Date
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($lessonPlans as $lessonPlan)

                        @php
                            $planFiles = $lessonPlan->all_files;
                        @endphp

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $lessonPlan->schoolClass->name ?? '—' }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $lessonPlan->subject->name ?? '—' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $lessonPlan->topic }}
                            </td>

                            <td class="px-6 py-4">

                                @if(count($planFiles))

                                    <div class="text-xs font-semibold text-gray-700">
                                        {{ count($planFiles) }} {{ \Illuminate\Support\Str::plural('file', count($planFiles)) }}
                                    </div>

                                    <ul class="mt-1 space-y-0.5">

                                        @foreach(array_slice($planFiles, 0, 2) as $file)
                                            <li>
                                                <a
                                                    href="{{ $file['url'] }}"
                                                    target="_blank"
                                                    title="{{ $file['name'] }}"
                                                    class="block max-w-[14rem] truncate text-xs font-medium text-indigo-600 hover:text-indigo-700"
                                                >
                                                    {{ $file['name'] }}
                                                </a>
                                            </li>
                                        @endforeach

                                    </ul>

                                    @if(count($planFiles) > 2)
                                        <div class="mt-1 text-xs text-gray-500">
                                            +{{ count($planFiles) - 2 }} more
                                        </div>
                                    @endif

                                @else

                                    <span class="text-xs text-gray-400">No files</span>

                                @endif

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $lessonPlan->lesson_date?->format('d M Y') ?? '—' }}
                            </td>

                            <td class="px-6 py-4">

                                @php
                                    $statusClasses = match($lessonPlan->status) {
                                        'approved' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'revision_requested' => 'bg-blue-100 text-blue-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };

                                    $statusLabel = match($lessonPlan->status) {
                                        'revision_requested' => 'Revision Requested',
                                        'pending' => 'Pending Vetting',
                                        default => ucfirst($lessonPlan->status),
                                    };
                                @endphp

                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>

                            </td>

                            <td class="px-6 py-4 text-right">

                                @if(in_array($lessonPlan->status, ['draft', 'revision_requested'], true))

                                    <div class="flex justify-end gap-2">

                                        <a
                                            href="{{ route('teacher.lesson-plans.edit', $lessonPlan) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('teacher.lesson-plans.submit', $lessonPlan) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                                            >
                                                Submit
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('teacher.lesson-plans.destroy', $lessonPlan) }}"
                                            onsubmit="return confirm('Delete this lesson plan and its files? This cannot be undone.')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
                                            >
                                                Delete
                                            </button>
                                        </form>

                                    </div>

                                @else

                                    <span class="text-xs text-gray-400">
                                        No action
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">

                                <div class="text-sm font-medium text-gray-900">
                                    No lesson plans yet
                                </div>

                                <p class="mt-1 text-sm text-gray-500">
                                    Create your first lesson plan to get started.
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
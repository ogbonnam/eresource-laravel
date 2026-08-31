@extends('layouts.dashboard')

@section('title', $resource->title)

@section('content')

<div class="space-y-8">

    {{-- Back --}}
    <div>
        <a
            href="{{ route('teacher.courses.show', $course) }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
        >
            ← Back to {{ $course->name }}
        </a>
    </div>


    {{-- Resource Header --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-6">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                <div>

                    {{-- Resource Type --}}
                    <div class="mb-3">

                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600">
                            {{ str_replace('_', ' ', $resource->type) }}
                        </span>

                        @if ($resource->is_published)

                            <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                Published
                            </span>

                        @else

                            <span class="ml-2 inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                Draft
                            </span>

                        @endif

                    </div>


                    {{-- Title --}}
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-900">
                        {{ $resource->title }}
                    </h1>


                    {{-- Course --}}
                    <p class="mt-2 text-sm text-slate-500">

                        {{ $course->subject?->name }}

                        @if ($course->schoolClass)
                            · {{ $course->schoolClass->name }}
                        @endif

                    </p>


                    {{-- Description --}}
                    @if ($resource->description)

                        <p class="mt-4 max-w-3xl leading-7 text-slate-600">
                            {{ $resource->description }}
                        </p>

                    @endif

                </div>


                {{-- Actions --}}
                <div class="flex shrink-0 gap-2">

                    <a
                        href="{{ route('teacher.courses.resources.edit', [$course, $resource]) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >

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
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 15.69a4.5 4.5 0 0 1-1.897 1.13L6 18l1.18-2.685a4.5 4.5 0 0 1 1.13-1.897l8.552-8.931Z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 7.125 16.875 4.5M18 14v4.75A2.25 2.25 0 0 1 15.75 21H6.25A2.25 2.25 0 0 1 4 18.75v-9.5A2.25 2.25 0 0 1 6.25 7H11"
                            />
                        </svg>

                        Edit

                    </a>

                </div>

            </div>

        </div>


        {{-- Resource Meta --}}
        <div class="grid gap-4 px-6 py-5 sm:grid-cols-3">

            <div>

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Type
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ ucwords(str_replace('_', ' ', $resource->type)) }}
                </p>

            </div>


            <div>

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Created
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ $resource->created_at?->format('M d, Y') }}
                </p>

            </div>


            <div>

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Last updated
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ $resource->updated_at?->format('M d, Y H:i') }}
                </p>

            </div>

        </div>

    </section>


    {{-- Main Resource Content --}}
    @if (
        in_array($resource->type, ['lesson_note', 'assignment'])
        && !empty($resource->content)
    )

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Resource Content
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Learning material contained in this resource.
                </p>

            </div>


            <article
                class="resource-content prose prose-slate max-w-none px-6 py-8
                       prose-headings:font-semibold
                       prose-h1:text-3xl
                       prose-h2:text-2xl
                       prose-h3:text-xl
                       prose-p:leading-7
                       prose-a:text-blue-600
                       prose-img:rounded-xl
                       prose-img:max-w-full
                       prose-table:w-full
                       prose-table:border-collapse
                       prose-th:border
                       prose-th:border-slate-200
                       prose-th:bg-slate-50
                       prose-th:px-3
                       prose-th:py-2
                       prose-td:border
                       prose-td:border-slate-200
                       prose-td:px-3
                       prose-td:py-2"
            >
                {!! $resource->content !!}
            </article>

        </section>

    @endif


    {{-- File Resource --}}
    @if ($resource->file_path)

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <p class="font-semibold text-slate-900">
                        Attached File
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Download or open the uploaded resource file.
                    </p>

                </div>


                <a
                    href="{{ asset('storage/' . $resource->file_path) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-5 w-5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 12 12 16.5m0 0 4.5-4.5M12 16.5V3"
                        />
                    </svg>

                    Open File

                </a>

            </div>

        </section>

    @endif


    {{-- External URL --}}
    @if ($resource->url)

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="min-w-0">

                    <p class="font-semibold text-slate-900">
                        External Resource
                    </p>

                    <p class="mt-1 truncate text-sm text-slate-500">
                        {{ $resource->url }}
                    </p>

                </div>


                <a
                    href="{{ $resource->url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                >

                    Open Resource

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
                            d="M13.5 6H19m0 0v5.5M19 6l-8 8"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 13.5v4.25A2.25 2.25 0 0 1 16.75 20H7.25A2.25 2.25 0 0 1 5 17.75v-9.5A2.25 2.25 0 0 1 7.25 6H11.5"
                        />
                    </svg>

                </a>

            </div>

        </section>

    @endif


    {{-- Empty content warning --}}
    @if (
        empty($resource->content)
        && empty($resource->file_path)
        && empty($resource->url)
    )

        <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">

            <p class="font-medium text-slate-700">
                This resource has no content yet.
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Edit the resource to add learning material.
            </p>

            <div class="mt-5">

                <a
                    href="{{ route('teacher.courses.resources.edit', [$course, $resource]) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                >
                    Edit Resource
                </a>

            </div>

        </section>

    @endif


    {{-- Footer navigation --}}
    <div class="flex items-center justify-between">

        <a
            href="{{ route('teacher.courses.show', $course) }}"
            class="text-sm font-medium text-slate-500 hover:text-slate-900"
        >
            ← Back to course
        </a>


        <a
            href="{{ route('teacher.courses.resources.edit', [$course, $resource]) }}"
            class="text-sm font-medium text-slate-900 hover:text-slate-600"
        >
            Edit resource →
        </a>

    </div>

</div>

@endsection


@push('styles')

<style>

    /*
     * Tiptap-generated content
     * Keeps pasted Word/Google Docs content
     * readable when displayed to teachers/students.
     */

    .resource-content {
        overflow-wrap: break-word;
        word-break: normal;
    }

    .resource-content img {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 1.5rem auto;
        border-radius: 0.75rem;
    }

    .resource-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }

    .resource-content th,
    .resource-content td {
        border: 1px solid #e2e8f0;
        padding: 0.75rem;
        vertical-align: top;
    }

    .resource-content th {
        background: #f8fafc;
        font-weight: 600;
    }

    .resource-content blockquote {
        margin: 1.5rem 0;
        border-left: 4px solid #cbd5e1;
        padding-left: 1rem;
        color: #475569;
    }

    .resource-content pre {
        overflow-x: auto;
        border-radius: 0.75rem;
        background: #0f172a;
        color: white;
        padding: 1rem;
    }

    .resource-content code {
        word-break: break-word;
    }

    .resource-content iframe {
        display: block;
        width: 100%;
        max-width: 100%;
        min-height: 400px;
        margin: 1.5rem 0;
        border-radius: 0.75rem;
    }

</style>

@endpush
@extends('layouts.dashboard')

@section('title', $resource->title)

@section('content')

<div class="mx-auto max-w-6xl space-y-8">

    {{-- ========================================================= --}}
    {{-- BACK TO COURSE --}}
    {{-- ========================================================= --}}

    <div>

        <a
            href="{{ route('student.courses.show', $course) }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
        >
            ← Back to {{ $course->name }}
        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- RESOURCE HEADER --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="flex flex-col gap-5">

            {{-- Resource badges --}}

            <div class="flex flex-wrap items-center gap-2">

                @if ($resource->type)

                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"
                    >
                        {{ ucfirst(str_replace('_', ' ', $resource->type)) }}
                    </span>

                @endif


                @if ($resource->is_published)

                    <span
                        class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
                    >
                        Published
                    </span>

                @endif

            </div>


            {{-- Title --}}

            <div>

                <h1 class="text-3xl font-semibold tracking-tight text-slate-900">
                    {{ $resource->title }}
                </h1>


                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">

                    <span>
                        {{ $course->name }}
                    </span>


                    @if ($course->subject)

                        <span>•</span>

                        <span>
                            {{ $course->subject->name }}
                        </span>

                    @endif


                    @if ($course->schoolClass)

                        <span>•</span>

                        <span>
                            {{ $course->schoolClass->name }}
                        </span>

                    @endif

                </div>

            </div>


            {{-- Description --}}

            @if ($resource->description)

                <p class="max-w-4xl leading-7 text-slate-500">
                    {{ $resource->description }}
                </p>

            @endif

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- RESOURCE CONTENT --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <h2 class="font-semibold text-slate-900">
                Learning Material
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ $course->name }}
            </p>

        </div>


        <div class="p-6">

            @php

                /*
                 * =====================================================
                 * FILE EXTENSION
                 * =====================================================
                 */

                $extension = $resource->file_path
                    ? strtolower(
                        pathinfo(
                            $resource->file_path,
                            PATHINFO_EXTENSION
                        )
                    )
                    : null;


                /*
                 * =====================================================
                 * PUBLIC FILE URL
                 * =====================================================
                 */

                $fileUrl = $resource->file_path
                    ? \Illuminate\Support\Facades\Storage::disk('public')
                        ->url($resource->file_path)
                    : null;


                /*
                 * =====================================================
                 * CHECK HTML CONTENT
                 * =====================================================
                 *
                 * Prevents empty HTML such as:
                 *
                 * <p></p>
                 *
                 * from being considered real content.
                 */

                $hasContent = !empty(
                    trim(
                        strip_tags(
                            str_replace(
                                ['&nbsp;', '&#160;'],
                                ' ',
                                $resource->content ?? ''
                            )
                        )
                    )
                );


                /*
                 * =====================================================
                 * EXTERNAL VIDEO
                 * =====================================================
                 *
                 * Change these values if your database uses a
                 * different resource type.
                 */

                $isExternalVideo = in_array(
                    $resource->type,
                    [
                        'video',
                        'external_video',
                        'youtube',
                        'youtube_video',
                        'vimeo',
                    ],
                    true
                );


                /*
                 * =====================================================
                 * THUMBNAIL URL
                 * =====================================================
                 *
                 * Supports:
                 *
                 * 1. Full URL
                 *    https://example.com/image.jpg
                 *
                 * 2. Storage path
                 *    resources/thumbnails/image.jpg
                 */

                $thumbnailUrl = null;

                if ($resource->thumbnail) {

                    if (
                        str_starts_with(
                            $resource->thumbnail,
                            'http://'
                        )
                        ||
                        str_starts_with(
                            $resource->thumbnail,
                            'https://'
                        )
                    ) {

                        $thumbnailUrl = $resource->thumbnail;

                    } else {

                        $thumbnailUrl =
                            \Illuminate\Support\Facades\Storage
                                ::disk('public')
                                ->url($resource->thumbnail);

                    }

                }

            @endphp


            {{-- ================================================= --}}
            {{-- LESSON NOTE / ASSIGNMENT CONTENT --}}
            {{-- ================================================= --}}

            @if (
                in_array(
                    $resource->type,
                    [
                        'lesson_note',
                        'assignment',
                    ],
                    true
                )
                && $hasContent
            )

                <article class="resource-content prose prose-slate max-w-none">

                    {!! $resource->content !!}

                </article>


            {{-- ================================================= --}}
            {{-- PDF --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->file_path
                && $extension === 'pdf'
            )

                <div class="space-y-4">

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100"
                    >

                        <iframe
                            src="{{ $fileUrl }}"
                            class="h-[800px] w-full"
                            title="{{ $resource->title }}"
                        ></iframe>

                    </div>


                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >

                        <p class="text-sm text-slate-500">
                            PDF document
                        </p>


                        <a
                            href="{{ $fileUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
                        >
                            Open PDF in New Tab

                            <span>
                                ↗
                            </span>

                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- UPLOADED VIDEO --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->file_path
                && in_array(
                    $extension,
                    [
                        'mp4',
                        'webm',
                        'ogg',
                        'mov',
                        'm4v',
                    ],
                    true
                )
            )

                <div class="space-y-4">

                    <div class="overflow-hidden rounded-2xl bg-black">

                        <video
                            controls
                            preload="metadata"
                            class="max-h-[750px] w-full"
                        >

                            <source
                                src="{{ $fileUrl }}"
                                type="video/{{ $extension === 'mov'
                                    ? 'quicktime'
                                    : $extension }}"
                            >

                            Your browser does not support video playback.

                        </video>

                    </div>


                    <div class="flex justify-end">

                        <a
                            href="{{ $fileUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            Open Video
                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- WORD DOCUMENT --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->file_path
                && in_array(
                    $extension,
                    [
                        'doc',
                        'docx',
                    ],
                    true
                )
            )

                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-10 text-center"
                >

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-8 w-8 text-blue-600"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 14.25v-7.5a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-3"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 3v4.5a1.5 1.5 0 0 0 1.5 1.5H21"
                            />

                        </svg>

                    </div>


                    <h3 class="mt-5 text-lg font-semibold text-slate-900">
                        {{ $resource->title }}
                    </h3>


                    <p
                        class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500"
                    >
                        This Word document is available for you to open or
                        download.
                    </p>


                    <div class="mt-6 flex flex-wrap justify-center gap-3">

                        <a
                            href="{{ $fileUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Open Document

                            <span>
                                ↗
                            </span>

                        </a>


                        <a
                            href="{{ $fileUrl }}"
                            download
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Download
                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- ZIP FILE --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->file_path
                && $extension === 'zip'
            )

                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-10 text-center"
                >

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-8 w-8 text-amber-600"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M20.25 7.5v10.125a2.625 2.625 0 0 1-2.625 2.625H6.375a2.625 2.625 0 0 1-2.625-2.625V7.5m16.5 0a2.25 2.25 0 0 0-2.625-2.625H6.375A2.25 2.25 0 0 0 3.75 7.5m16.5 0H3.75"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 10.5h6M9 14h6"
                            />

                        </svg>

                    </div>


                    <h3 class="mt-5 text-lg font-semibold text-slate-900">
                        {{ $resource->title }}
                    </h3>


                    <p
                        class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500"
                    >
                        This ZIP archive contains downloadable learning
                        materials.
                    </p>


                    <div class="mt-6">

                        <a
                            href="{{ $fileUrl }}"
                            download
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Download ZIP

                            <span>
                                ↓
                            </span>

                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- POWERPOINT --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->file_path
                && in_array(
                    $extension,
                    [
                        'ppt',
                        'pptx',
                    ],
                    true
                )
            )

                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-10 text-center"
                >

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-8 w-8 text-orange-600"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 4.5h18M3 19.5h18M5.25 4.5v15M18.75 4.5v15"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 8.25h6M9 12h6M9 15.75h3"
                            />

                        </svg>

                    </div>


                    <h3 class="mt-5 text-lg font-semibold text-slate-900">
                        {{ $resource->title }}
                    </h3>


                    <p
                        class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500"
                    >
                        This presentation is available as a PowerPoint file.
                    </p>


                    <div class="mt-6 flex flex-wrap justify-center gap-3">

                        <a
                            href="{{ $fileUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Open Presentation
                        </a>


                        <a
                            href="{{ $fileUrl }}"
                            download
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Download
                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- IMAGE --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->file_path
                && in_array(
                    $extension,
                    [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'webp',
                        'svg',
                    ],
                    true
                )
            )

                <div class="space-y-4">

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-4"
                    >

                        <img
                            src="{{ $fileUrl }}"
                            alt="{{ $resource->title }}"
                            class="mx-auto max-h-[800px] max-w-full rounded-xl object-contain"
                        >

                    </div>


                    <div class="flex justify-end">

                        <a
                            href="{{ $fileUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                        >
                            Open Image
                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- EXTERNAL VIDEO WITH THUMBNAIL --}}
            {{-- ================================================= --}}

            @elseif (
                $resource->url
                && $isExternalVideo
                && $thumbnailUrl
            )

                <div class="space-y-5">

                    {{-- Video thumbnail --}}

                    <a
                        href="{{ $resource->url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group relative block overflow-hidden rounded-2xl border border-slate-200 bg-black shadow-sm"
                    >

                        <img
                            src="{{ $thumbnailUrl }}"
                            alt="{{ $resource->title }}"
                            class="aspect-video w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                        >


                        {{-- Overlay --}}

                        <div
                            class="absolute inset-0 bg-black/20 transition duration-300 group-hover:bg-black/30"
                        ></div>


                        {{-- Play button --}}

                        <div
                            class="absolute inset-0 flex items-center justify-center"
                        >

                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-white/95 shadow-xl transition duration-300 group-hover:scale-110"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="ml-1 h-9 w-9 text-slate-900"
                                >

                                    <path
                                        d="M8.25 5.25a1.5 1.5 0 0 1 2.25-1.299l8.25 4.763a1.5 1.5 0 0 1 0 2.598l-8.25 4.763a1.5 1.5 0 0 1-2.25-1.299V5.25Z"
                                    />

                                </svg>

                            </div>

                        </div>


                        {{-- Video label --}}

                        <div
                            class="absolute bottom-4 left-4"
                        >

                            <span
                                class="inline-flex items-center rounded-full bg-black/70 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur"
                            >
                                External Video
                            </span>

                        </div>

                    </a>


                    {{-- Video information --}}

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <div>

                            <p class="text-sm font-medium text-slate-900">
                                {{ $resource->title }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Click the thumbnail to watch the video.
                            </p>

                        </div>


                        <a
                            href="{{ $resource->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Watch Video

                            <span>
                                ↗
                            </span>

                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- NORMAL EXTERNAL RESOURCE --}}
            {{-- ================================================= --}}

            @elseif ($resource->url)

                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-10 text-center"
                >

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-8 w-8 text-blue-600"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 6H18a2.25 2.25 0 0 1 2.25 2.25v7.5A2.25 2.25 0 0 1 18 18h-7.5a2.25 2.25 0 0 1-2.25-2.25V13.5"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 3.75h4.5v4.5M20.25 3.75 12 12"
                            />

                        </svg>

                    </div>


                    <h3 class="mt-5 text-lg font-semibold text-slate-900">
                        External Learning Resource
                    </h3>


                    <p
                        class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-500"
                    >
                        Your teacher has provided an external website or
                        learning resource.
                    </p>


                    <div class="mt-6">

                        <a
                            href="{{ $resource->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Open External Resource

                            <span>
                                ↗
                            </span>

                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- GENERIC FILE --}}
            {{-- ================================================= --}}

            @elseif ($resource->file_path)

                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-10 text-center"
                >

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-8 w-8 text-slate-600"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 14.25v-7.5a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-3"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 3v4.5a1.5 1.5 0 0 0 1.5 1.5H21"
                            />

                        </svg>

                    </div>


                    <h3 class="mt-5 text-lg font-semibold text-slate-900">
                        {{ $resource->title }}
                    </h3>


                    <p class="mt-2 text-sm text-slate-500">
                        {{ strtoupper($extension ?? 'FILE') }} file
                    </p>


                    <div class="mt-6">

                        <a
                            href="{{ $fileUrl }}"
                            download
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Download Resource

                            <span>
                                ↓
                            </span>

                        </a>

                    </div>

                </div>


            {{-- ================================================= --}}
            {{-- CONTENT FALLBACK --}}
            {{-- ================================================= --}}

            @elseif ($hasContent)

                <article class="resource-content prose prose-slate max-w-none">

                    {!! $resource->content !!}

                </article>


            {{-- ================================================= --}}
            {{-- NOTHING AVAILABLE --}}
            {{-- ================================================= --}}

            @else

                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center"
                >

                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100"
                    >

                        <span class="text-2xl">
                            📄
                        </span>

                    </div>


                    <p class="mt-4 font-medium text-slate-700">
                        No content available
                    </p>


                    <p class="mt-1 text-sm text-slate-500">
                        Your teacher has not added content to this resource yet.
                    </p>

                </div>

            @endif

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- COURSE INFORMATION --}}
    {{-- ========================================================= --}}

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="font-semibold text-slate-900">
            Course Information
        </h2>


        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Course --}}

            <div>

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Course
                </p>


                <p class="mt-1 text-sm font-medium text-slate-900">
                    {{ $course->name }}
                </p>

            </div>


            {{-- Subject --}}

            @if ($course->subject)

                <div>

                    <p
                        class="text-xs font-medium uppercase tracking-wide text-slate-400"
                    >
                        Subject
                    </p>


                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $course->subject->name }}
                    </p>

                </div>

            @endif


            {{-- Class --}}

            @if ($course->schoolClass)

                <div>

                    <p
                        class="text-xs font-medium uppercase tracking-wide text-slate-400"
                    >
                        Class
                    </p>


                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $course->schoolClass->name }}
                    </p>

                </div>

            @endif

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- BACK TO COURSE --}}
    {{-- ========================================================= --}}

    <div>

        <a
            href="{{ route('student.courses.show', $course) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            ← Back to Course
        </a>

    </div>

</div>

@endsection


@push('styles')

<style>

    /* =========================================================
       RESOURCE CONTENT
       ========================================================= */

    .resource-content {
        color: rgb(30 41 59);
        font-size: 16px;
        line-height: 1.8;
    }


    /* =========================================================
       PARAGRAPHS
       ========================================================= */

    .resource-content p {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }


    /* =========================================================
       HEADINGS
       ========================================================= */

    .resource-content h1 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-size: 2rem;
        line-height: 1.2;
        font-weight: 700;
        color: rgb(15 23 42);
    }


    .resource-content h2 {
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
        line-height: 1.3;
        font-weight: 700;
        color: rgb(15 23 42);
    }


    .resource-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
        line-height: 1.4;
        font-weight: 600;
        color: rgb(15 23 42);
    }


    /* =========================================================
       LISTS
       ========================================================= */

    .resource-content ul {
        margin: 1rem 0;
        padding-left: 1.75rem;
        list-style-type: disc;
    }


    .resource-content ol {
        margin: 1rem 0;
        padding-left: 1.75rem;
        list-style-type: decimal;
    }


    .resource-content li {
        margin-top: 0.35rem;
        margin-bottom: 0.35rem;
    }


    /* =========================================================
       LINKS
       ========================================================= */

    .resource-content a {
        color: rgb(37 99 235);
        text-decoration: underline;
    }


    /* =========================================================
       BLOCKQUOTES
       ========================================================= */

    .resource-content blockquote {
        margin: 1.5rem 0;
        border-left: 4px solid rgb(203 213 225);
        padding-left: 1rem;
        color: rgb(71 85 105);
    }


    /* =========================================================
       IMAGES
       ========================================================= */

    .resource-content img {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 1.5rem auto;
        border-radius: 0.75rem;
    }


    /* =========================================================
       TABLES
       ========================================================= */

    .resource-content table {
        width: 100%;
        margin: 1.5rem 0;
        border-collapse: collapse;
        overflow: hidden;
        border-radius: 0.75rem;
    }


    .resource-content th,
    .resource-content td {
        border: 1px solid rgb(203 213 225);
        padding: 0.75rem;
        text-align: left;
        vertical-align: top;
    }


    .resource-content th {
        background: rgb(241 245 249);
        font-weight: 600;
    }


    /* =========================================================
       IFRAME
       ========================================================= */

    iframe {
        max-width: 100%;
    }

</style>

@endpush

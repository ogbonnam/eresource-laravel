@extends('layouts.dashboard')

@section('title', 'Edit Resource · ' . $resource->title)

@section('content')

<div class="mx-auto max-w-5xl space-y-8">

    <section>
        <a
            href="{{ route('teacher.courses.resources.show', [$course, $resource]) }}"
            class="text-sm font-medium text-slate-500 hover:text-slate-900"
        >
            ← Back to {{ $resource->title }}
        </a>

        <div class="mt-6">
            <p class="text-sm text-slate-500">
                {{ $course->subject?->name }}
                @if($course->schoolClass)
                    · {{ $course->schoolClass->name }}
                @endif
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight">
                Edit Resource
            </h1>

            <p class="mt-2 text-slate-500">
                Update this learning resource.
            </p>
        </div>
    </section>


    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <p class="font-semibold text-red-800">
                Please fix the following:
            </p>

            <ul class="mt-2 list-disc pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form
        method="POST"
        action="{{ route('teacher.courses.resources.update', [$course, $resource]) }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf
        @method('PUT')


        {{-- Basic Information --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-semibold text-slate-900">
                    Resource Information
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Update the title, description and resource type.
                </p>
            </div>


            <div class="space-y-6 p-6">

                <div>
                    <label
                        for="title"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Title
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $resource->title) }}"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >
                </div>


                <div>
                    <label
                        for="description"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Short Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >{{ old('description', $resource->description) }}</textarea>
                </div>


                <div>
                    <label
                        for="type"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Resource Type
                    </label>

                    <select
                        id="type"
                        name="type"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >

                        <option value="lesson_note"
                            @selected(old('type', $resource->type) === 'lesson_note')>
                            Lesson Note
                        </option>

                        <option value="assignment"
                            @selected(old('type', $resource->type) === 'assignment')>
                            Assignment
                        </option>

                        <option value="pdf"
                            @selected(old('type', $resource->type) === 'pdf')>
                            PDF
                        </option>

                        <option value="document"
                            @selected(old('type', $resource->type) === 'document')>
                            Document
                        </option>

                        <option value="video"
                            @selected(old('type', $resource->type) === 'video')>
                            Video
                        </option>

                        <option value="zip"
                            @selected(old('type', $resource->type) === 'zip')>
                            ZIP
                        </option>

                    </select>
                </div>

            </div>

        </section>


        {{-- Content --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Content
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Edit the lesson or assignment content.
                </p>

            </div>


            <div class="p-6">

                <div
                    id="resource-editor"
                    data-image-upload-url="{{ route('teacher.courses.resources.images.store', $course) }}"
                    class="tiptap-editor min-h-[500px] rounded-xl border border-slate-300 bg-white px-6 py-5"
                ></div>


                <textarea
                    id="resource-content"
                    name="content"
                    class="hidden"
                >{{ old('content', $resource->content) }}</textarea>

            </div>

        </section>


        {{-- File --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    File / External Resource
                </h2>

            </div>


            <div class="space-y-6 p-6">

                <div>

                    <label
                        for="file"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Replace File
                    </label>

                    <input
                        id="file"
                        name="file"
                        type="file"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white text-sm"
                    >

                    @if($resource->file_path)

                        <p class="mt-2 text-xs text-slate-500">
                            A file is currently attached.
                            Upload a new file only if you want to replace it.
                        </p>

                    @endif

                </div>


                <div>

                    <label
                        for="url"
                        class="block text-sm font-medium text-slate-700"
                    >
                        External URL
                    </label>

                    <input
                        id="url"
                        name="url"
                        type="url"
                        value="{{ old('url', $resource->url) }}"
                        placeholder="https://..."
                        class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >

                </div>


                <div>

                    <label
                        for="thumbnail"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Replace Thumbnail
                    </label>

                    <input
                        id="thumbnail"
                        name="thumbnail"
                        type="file"
                        accept="image/*"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white text-sm"
                    >

                </div>

            </div>

        </section>


        {{-- Publishing --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="p-6">

                <label class="flex cursor-pointer items-start gap-3">

                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published', $resource->is_published))
                        class="mt-1 h-4 w-4 rounded border-slate-300"
                    >

                    <span>

                        <span class="block text-sm font-medium text-slate-900">
                            Published
                        </span>

                        <span class="mt-1 block text-sm text-slate-500">
                            Students can see this resource when published.
                        </span>

                    </span>

                </label>

            </div>

        </section>


        {{-- Actions --}}
        <div class="flex items-center justify-between">

            <a
                href="{{ route('teacher.courses.resources.show', [$course, $resource]) }}"
                class="rounded-xl px-5 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800"
            >
                Update Resource
            </button>

        </div>

    </form>

</div>

@endsection
@extends('layouts.dashboard')

@section('title', 'Add Resource · ' . $course->name)

@section('content')

<div class="mx-auto max-w-5xl space-y-8">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <section>

        <a
            href="{{ route('teacher.courses.show', $course) }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900"
        >
            ← Back to {{ $course->name }}
        </a>

        <div class="mt-6">

            <p class="text-sm font-medium text-slate-500">
                {{ $course->subject?->name }}
                ·
                {{ $course->schoolClass?->name }}
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                Add Resource
            </h1>

            <p class="mt-2 text-slate-500">
                Add learning material to this course.
            </p>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

            <p class="font-semibold text-red-800">
                Please fix the following:
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- RESOURCE FORM --}}
    {{-- ========================================================= --}}

    <form
        id="resource-form"
        method="POST"
        action="{{ route('teacher.courses.resources.store', $course) }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf


        {{-- ===================================================== --}}
        {{-- BASIC INFORMATION --}}
        {{-- ===================================================== --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Resource Information
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Give your resource a title and choose its type.
                </p>

            </div>


            <div class="space-y-6 p-6">

                {{-- Title --}}

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
                        value="{{ old('title') }}"
                        required
                        placeholder="e.g. Introduction to Cell Structure"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >

                </div>


                {{-- Description --}}

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
                        rows="3"
                        placeholder="Briefly describe this resource..."
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >{{ old('description') }}</textarea>

                </div>


                {{-- Type --}}

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
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >

                        <option value="">
                            Select resource type
                        </option>

                        <option
                            value="lesson_note"
                            @selected(old('type') === 'lesson_note')
                        >
                            Lesson Note
                        </option>

                        <option
                            value="assignment"
                            @selected(old('type') === 'assignment')
                        >
                            Assignment
                        </option>

                        <option
                            value="pdf"
                            @selected(old('type') === 'pdf')
                        >
                            PDF
                        </option>

                        <option
                            value="document"
                            @selected(old('type') === 'document')
                        >
                            Document
                        </option>

                        <option
                            value="video"
                            @selected(old('type') === 'video')
                        >
                            Video
                        </option>

                        <option
                            value="zip"
                            @selected(old('type') === 'zip')
                        >
                            ZIP
                        </option>

                    </select>

                </div>

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- WYSIWYG EDITOR --}}
        {{-- ========================================================= --}}

        <section
            id="editor-section"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    Content
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Write your lesson or assignment content. You can paste formatted
                    content and images directly from Word or another document.
                </p>

            </div>


            <div class="p-6">

                {{-- Toolbar --}}

                <div
                    class="flex flex-wrap items-center gap-1 rounded-t-xl border border-slate-300 bg-slate-50 p-2"
                >

                    {{-- Paragraph --}}

                    <button
                        type="button"
                        data-editor-command="paragraph"
                        class="editor-button"
                        title="Paragraph"
                    >
                        ¶
                    </button>


                    {{-- Headings --}}

                    <button
                        type="button"
                        data-editor-command="heading1"
                        class="editor-button font-bold"
                        title="Heading 1"
                    >
                        H1
                    </button>

                    <button
                        type="button"
                        data-editor-command="heading2"
                        class="editor-button font-bold"
                        title="Heading 2"
                    >
                        H2
                    </button>

                    <button
                        type="button"
                        data-editor-command="heading3"
                        class="editor-button font-bold"
                        title="Heading 3"
                    >
                        H3
                    </button>


                    <div class="mx-1 h-6 w-px bg-slate-300"></div>


                    {{-- Formatting --}}

                    <button
                        type="button"
                        data-editor-command="bold"
                        class="editor-button font-bold"
                        title="Bold"
                    >
                        B
                    </button>

                    <button
                        type="button"
                        data-editor-command="italic"
                        class="editor-button italic"
                        title="Italic"
                    >
                        I
                    </button>

                    <button
                        type="button"
                        data-editor-command="underline"
                        class="editor-button underline"
                        title="Underline"
                    >
                        U
                    </button>

                    <button
                        type="button"
                        data-editor-command="strike"
                        class="editor-button line-through"
                        title="Strikethrough"
                    >
                        S
                    </button>


                    <div class="mx-1 h-6 w-px bg-slate-300"></div>


                    {{-- Lists --}}

                    <button
                        type="button"
                        data-editor-command="bulletList"
                        class="editor-button"
                        title="Bullet List"
                    >
                        • List
                    </button>

                    <button
                        type="button"
                        data-editor-command="orderedList"
                        class="editor-button"
                        title="Numbered List"
                    >
                        1. List
                    </button>


                    {{-- Blockquote --}}

                    <button
                        type="button"
                        data-editor-command="blockquote"
                        class="editor-button"
                        title="Blockquote"
                    >
                        Quote
                    </button>


                    <div class="mx-1 h-6 w-px bg-slate-300"></div>


                    {{-- Alignment --}}

                    <button
                        type="button"
                        data-editor-command="alignLeft"
                        class="editor-button"
                        title="Align Left"
                    >
                        ←
                    </button>

                    <button
                        type="button"
                        data-editor-command="alignCenter"
                        class="editor-button"
                        title="Align Center"
                    >
                        ↔
                    </button>

                    <button
                        type="button"
                        data-editor-command="alignRight"
                        class="editor-button"
                        title="Align Right"
                    >
                        →
                    </button>


                    <div class="mx-1 h-6 w-px bg-slate-300"></div>


                    {{-- Link --}}

                    <button
                        type="button"
                        data-editor-command="link"
                        class="editor-button"
                        title="Add Link"
                    >
                        Link
                    </button>


                    {{-- Image --}}

                    <button
                        type="button"
                        data-editor-command="image"
                        class="editor-button"
                        title="Insert Image"
                    >
                        Image
                    </button>


                    {{-- Table --}}

                    <button
                        type="button"
                        data-editor-command="table"
                        class="editor-button"
                        title="Insert Table"
                    >
                        Table
                    </button>


                    <div class="mx-1 h-6 w-px bg-slate-300"></div>


                    {{-- Undo / Redo --}}

                    <button
                        type="button"
                        data-editor-command="undo"
                        class="editor-button"
                        title="Undo"
                    >
                        ↶
                    </button>

                    <button
                        type="button"
                        data-editor-command="redo"
                        class="editor-button"
                        title="Redo"
                    >
                        ↷
                    </button>

                </div>


                {{-- Editor --}}

                <div
                    id="resource-editor"
                    data-image-upload-url="{{ route('teacher.courses.resources.images.store', $course) }}"
                    class="tiptap-editor min-h-[500px] rounded-b-xl border border-t-0 border-slate-300 bg-white px-6 py-5"
                ></div>


                {{-- Hidden HTML --}}

                <textarea
                    id="resource-content"
                    name="content"
                    class="hidden"
                >{{ old('content') }}</textarea>


                <p class="mt-3 text-xs text-slate-500">
                    Tip: You can paste text and images directly from Word,
                    Google Docs or another document.
                </p>

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- FILE / EXTERNAL RESOURCE --}}
        {{-- ========================================================= --}}

        <section
            id="file-section"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    File / External Resource
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Use this area for PDFs, documents, videos and ZIP files.
                </p>

            </div>


            <div class="space-y-6 p-6">

                {{-- File --}}

                <div>

                    <label
                        for="file"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Upload File
                    </label>

                    <input
                        id="file"
                        name="file"
                        type="file"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200"
                    >

                    <p class="mt-2 text-xs text-slate-500">
                        Maximum file size: 50MB.
                    </p>

                </div>


                {{-- URL --}}

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
                        value="{{ old('url') }}"
                        placeholder="https://..."
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    >

                </div>


                {{-- Thumbnail --}}

                <div>

                    <label
                        for="thumbnail"
                        class="block text-sm font-medium text-slate-700"
                    >
                        Thumbnail
                    </label>

                    <input
                        id="thumbnail"
                        name="thumbnail"
                        type="file"
                        accept="image/*"
                        class="mt-2 block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200"
                    >

                </div>

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- PUBLISHING --}}
        {{-- ========================================================= --}}

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="p-6">

                <label class="flex cursor-pointer items-start gap-3">

                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published'))
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300"
                    >

                    <span>

                        <span class="block text-sm font-medium text-slate-900">
                            Publish immediately
                        </span>

                        <span class="mt-1 block text-sm text-slate-500">
                            Students will be able to see this resource immediately.
                        </span>

                    </span>

                </label>

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- ACTIONS --}}
        {{-- ========================================================= --}}

        <div class="flex items-center justify-between">

            <a
                href="{{ route('teacher.courses.show', $course) }}"
                class="rounded-xl px-5 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
            >
                Save Resource
            </button>

        </div>

    </form>

</div>

@endsection


@push('styles')

<style>

    /* =========================================================
       EDITOR BUTTONS
       ========================================================= */

    .editor-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 34px;
        padding: 0 9px;
        border-radius: 8px;
        color: rgb(51 65 85);
        font-size: 13px;
        font-weight: 500;
        transition: background-color 150ms ease;
        cursor: pointer;
    }

    .editor-button:hover {
        background-color: rgb(226 232 240);
    }


    /* =========================================================
       TIPTAP DOCUMENT
       ========================================================= */

    .tiptap-editor .ProseMirror {
        min-height: 460px;
        outline: none;
        font-size: 16px;
        line-height: 1.75;
        color: rgb(15 23 42);
        cursor: text;
    }

    .tiptap-editor .ProseMirror:focus {
        outline: none;
    }


    /* =========================================================
       PARAGRAPHS
       ========================================================= */

    .tiptap-editor p {
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
    }


    /* =========================================================
       HEADINGS
       ========================================================= */

    .tiptap-editor h1 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-size: 2rem;
        line-height: 1.2;
        font-weight: 700;
    }

    .tiptap-editor h2 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
        line-height: 1.3;
        font-weight: 700;
    }

    .tiptap-editor h3 {
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
        line-height: 1.4;
        font-weight: 600;
    }


    /* =========================================================
       LISTS
       ========================================================= */

    .tiptap-editor ul {
        list-style-type: disc;
        margin: 1rem 0;
        padding-left: 1.75rem;
    }

    .tiptap-editor ol {
        list-style-type: decimal;
        margin: 1rem 0;
        padding-left: 1.75rem;
    }


    /* =========================================================
       LINKS
       ========================================================= */

    .tiptap-editor a {
        color: rgb(37 99 235);
        text-decoration: underline;
    }


    /* =========================================================
       BLOCKQUOTE
       ========================================================= */

    .tiptap-editor blockquote {
        margin: 1rem 0;
        border-left: 4px solid rgb(203 213 225);
        padding-left: 1rem;
        color: rgb(71 85 105);
    }


    /* =========================================================
       IMAGES
       ========================================================= */

    .tiptap-editor img {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 1.5rem auto;
        border-radius: 0.75rem;
    }


    /* =========================================================
       TABLES
       ========================================================= */

    .tiptap-editor table {
        width: 100%;
        margin: 1.5rem 0;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .tiptap-editor td,
    .tiptap-editor th {
        min-width: 1em;
        border: 1px solid rgb(203 213 225);
        padding: 8px;
        vertical-align: top;
    }

    .tiptap-editor th {
        background: rgb(241 245 249);
        font-weight: 600;
    }

</style>

@endpush
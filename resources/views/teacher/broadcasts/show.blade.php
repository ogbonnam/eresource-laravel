@extends('layouts.dashboard')

@section('title', $broadcast->title)

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    <div>

        <a
            href="{{ route('teacher.broadcasts.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900"
        >

            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                />
            </svg>

            Back to broadcasts

        </a>

    </div>


    @if(session('success'))

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>

    @endif


    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
            {{ $broadcast->title }}
        </h1>


        <div class="mt-4 space-y-1 text-sm text-slate-500">

            <p>
                From:

                <span class="font-medium text-slate-700">
                    {{ $broadcast->sender?->name ?? 'School Administration' }}
                </span>
            </p>

            <p>
                Sent:
                {{ $broadcast->sent_at?->format('d M Y, h:i A') }}
            </p>

            @if($broadcast->department)

                <p>
                    Department:
                    {{ $broadcast->department->name }}
                </p>

            @endif

        </div>

    </div>


    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <h2 class="text-lg font-semibold text-slate-900">
            Message
        </h2>

        <div class="mt-5 whitespace-pre-wrap text-sm leading-7 text-slate-700">
            {{ $broadcast->message }}
        </div>

    </div>


    @if($broadcast->attachments->isNotEmpty())

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <h2 class="text-lg font-semibold text-slate-900">
                Attachments
            </h2>


            <div class="mt-5 space-y-3">

                @foreach($broadcast->attachments as $attachment)

                    <a
                        href="{{ route('teacher.broadcasts.attachment', $attachment) }}"
                        target="_blank"
                        rel="noopener"
                        class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50"
                    >

                        <div class="flex min-w-0 items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">

                                <svg
                                    class="h-5 w-5 text-slate-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 18h10a2 2 0 002-2V8.828a2 2 0 00-.586-1.414l-3.828-3.828A2 2 0 0013.172 3H7a2 2 0 00-2 2v11a2 2 0 002 2z"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 3v5h5"
                                    />
                                </svg>

                            </div>


                            <div class="min-w-0">

                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $attachment->file_name }}
                                </p>

                                @if($attachment->file_size)

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ number_format($attachment->file_size / 1024 / 1024, 2) }}
                                        MB
                                    </p>

                                @endif

                            </div>

                        </div>


                        <span class="shrink-0 text-sm font-semibold text-blue-600">
                            Open
                        </span>

                    </a>

                @endforeach

            </div>

        </div>

    @endif


    @if($broadcast->google_sheet_url)

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 sm:p-8">

            <h2 class="text-lg font-semibold text-slate-900">
                Google Sheet
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                Open the Google Sheet using the button below.
                Your Google account permissions determine whether you can
                view or edit the sheet.
            </p>


            <div class="mt-5">

                <a
                    href="{{ route('teacher.broadcasts.sheet', $broadcast) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >

                    Open Google Sheet

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14 5h5m0 0v5m0-5l-7 7"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 14v4a1 1 0 01-1 1H6a1 1 0 01-1-1V6a1 1 0 011-1h4"
                        />
                    </svg>

                </a>

            </div>

        </div>

    @endif


    @if(! $recipient->acknowledged_at)

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 sm:p-8">

            <h2 class="font-semibold text-slate-900">
                Acknowledgement
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                If you have read and understood this circular,
                you can acknowledge it.
            </p>


            <form
                method="POST"
                action="{{ route('teacher.broadcasts.acknowledge', $broadcast) }}"
                class="mt-5"
            >

                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                >
                    Acknowledge Broadcast
                </button>

            </form>

        </div>

    @else

        <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4">

            <p class="text-sm font-medium text-blue-800">

                You acknowledged this broadcast on

                {{ $recipient->acknowledged_at->format('d M Y, h:i A') }}.

            </p>

        </div>

    @endif

</div>

@endsection
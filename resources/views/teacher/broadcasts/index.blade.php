@extends('layouts.dashboard')

@section('title', 'My Broadcasts')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            My Broadcasts
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            School circulars, announcements and documents sent to you.
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($broadcasts->count())

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="divide-y divide-slate-200">

                @foreach($broadcasts as $broadcast)

                    @php
                        $recipient = $broadcast->recipients->first();
                        $isRead = filled($recipient?->read_at);
                    @endphp

                    <a
                        href="{{ route('teacher.broadcasts.show', $broadcast) }}"
                        class="block px-5 py-5 transition hover:bg-slate-50 sm:px-6"
                    >

                        <div class="flex items-start gap-4">

                            <div class="pt-2">

                                @if(! $isRead)

                                    <span class="block h-3 w-3 rounded-full bg-blue-600"></span>

                                @else

                                    <span class="block h-3 w-3 rounded-full bg-slate-200"></span>

                                @endif

                            </div>


                            <div class="min-w-0 flex-1">

                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                                    <h2
                                        class="{{ $isRead ? 'font-medium' : 'font-bold' }}
                                        truncate text-slate-900"
                                    >
                                        {{ $broadcast->title }}
                                    </h2>

                                    <span class="shrink-0 text-xs text-slate-400">
                                        {{ $broadcast->sent_at?->format('d M Y, h:i A') }}
                                    </span>

                                </div>


                                <p class="mt-1 text-sm text-slate-500">

                                    From:

                                    <span class="font-medium">
                                        {{ $broadcast->sender?->name ?? 'School Administration' }}
                                    </span>

                                </p>


                                @if($broadcast->department)

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $broadcast->department->name }}
                                    </p>

                                @endif


                                <div class="mt-3 flex flex-wrap gap-2">

                                    @if($broadcast->attachments->count())

                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">

                                            {{ $broadcast->attachments->count() }}

                                            {{ Str::plural(
                                                'attachment',
                                                $broadcast->attachments->count()
                                            ) }}

                                        </span>

                                    @endif


                                    @if($broadcast->google_sheet_url)

                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            Google Sheet
                                        </span>

                                    @endif


                                    @if($recipient?->acknowledged_at)

                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            Acknowledged
                                        </span>

                                    @endif

                                </div>

                            </div>


                            <div class="pt-1 text-slate-400">

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>

                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        </div>


        <div>
            {{ $broadcasts->links() }}
        </div>


    @else

        <div class="rounded-xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">

            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">

                <svg
                    class="h-6 w-6 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2h-1M5 11V9a2 2 0 012-2h1m4-4h2a2 2 0 012 2v2H8V5a2 2 0 012-2h2z"
                    />
                </svg>

            </div>

            <h2 class="mt-4 text-lg font-semibold text-slate-900">
                No broadcasts yet
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                School circulars and announcements sent to you will appear here.
            </p>

        </div>

    @endif

</div>

@endsection
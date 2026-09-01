<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>eResource | Learning Made Simple</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
        }

        .hero-grid {
            background-image:
                linear-gradient(to right, rgba(148, 163, 184, 0.10) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(148, 163, 184, 0.10) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .hero-glow {
            background:
                radial-gradient(circle at 50% 20%, rgba(59, 130, 246, 0.16), transparent 35%),
                radial-gradient(circle at 80% 50%, rgba(99, 102, 241, 0.12), transparent 30%);
        }

        .floating-card {
            animation: float 5s ease-in-out infinite;
        }

        .floating-card-delay {
            animation: float 5s ease-in-out 1.2s infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased">

    {{-- =========================================================
         NAVIGATION
    ========================================================== --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-6 lg:px-8">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-600/20">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    </svg>
                </div>

                <div>
                    <div class="text-lg font-bold tracking-tight text-slate-900">
                        eResource
                    </div>
                    <div class="-mt-1 text-[10px] font-medium uppercase tracking-widest text-slate-500">
                        Learning Platform
                    </div>
                </div>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">
                    Features
                </a>

                <a href="#assignments" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">
                    Assignments
                </a>

                <a href="#discussions" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">
                    Discussions
                </a>

                <a href="#how-it-works" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">
                    How it works
                </a>
            </nav>

            {{-- Login --}}
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                        Dashboard

                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                        Sign in

                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>


    {{-- =========================================================
         HERO
    ========================================================== --}}
    <main>

        <section class="relative overflow-hidden pt-16">

            <div class="absolute inset-0 hero-grid"></div>
            <div class="absolute inset-0 hero-glow"></div>

            <div class="relative mx-auto max-w-7xl px-5 pb-20 pt-20 sm:px-6 sm:pt-28 lg:px-8 lg:pb-28">

                <div class="grid items-center gap-16 lg:grid-cols-2">

                    {{-- Hero text --}}
                    <div class="max-w-2xl">

                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700">
                            <span class="flex h-2 w-2 rounded-full bg-blue-600"></span>
                            A smarter way to learn
                        </div>

                        <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                            Everything you need to
                            <span class="text-blue-600">learn, teach and connect.</span>
                        </h1>

                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                            eResource brings lessons, learning materials, assignments and
                            class discussions together in one simple learning platform.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700">
                                    Go to Dashboard

                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700">
                                    Sign in with Google

                                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M21.35 12.27c0-.78-.07-1.53-.2-2.25H12v4.26h5.22a4.46 4.46 0 0 1-1.94 2.93v2.43h3.14c1.84-1.69 2.93-4.18 2.93-7.37z"/>
                                        <path fill="currentColor" d="M12 21.99c2.63 0 4.84-.87 6.45-2.35l-3.14-2.43c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.5A9.74 9.74 0 0 0 12 21.99z"/>
                                        <path fill="currentColor" d="M6.54 14.1A5.85 5.85 0 0 1 6.23 12c0-.73.13-1.44.31-2.1V7.4H3.3A10 10 0 0 0 2 12c0 1.61.39 3.13 1.3 4.6l3.24-2.5z"/>
                                        <path fill="currentColor" d="M12 5.87c1.43 0 2.71.49 3.72 1.46l2.79-2.79C16.84 2.93 14.63 2 12 2a9.74 9.74 0 0 0-8.7 5.4l3.24 2.5C7.31 7.59 9.46 5.87 12 5.87z"/>
                                    </svg>
                                </a>
                            @endauth

                            <a href="#features"
                               class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                Explore eResource
                            </a>

                        </div>

                        <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                One place for learning
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Built for schools
                            </div>
                        </div>

                    </div>


                    {{-- Hero dashboard preview --}}
                    <div class="relative mx-auto w-full max-w-xl lg:ml-auto">

                        <div class="absolute -inset-6 rounded-[2rem] bg-blue-100/60 blur-3xl"></div>

                        <div class="relative rounded-3xl border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/10 sm:p-6">

                            {{-- Fake browser header --}}
                            <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
                                <div class="flex gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                </div>

                                <span class="text-xs font-medium text-slate-400">
                                    eResource
                                </span>

                                <div class="h-6 w-6 rounded-full bg-blue-100"></div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">

                                <div class="col-span-2 rounded-2xl bg-blue-600 p-5 text-white">
                                    <div class="text-xs font-medium text-blue-100">
                                        Welcome back
                                    </div>

                                    <div class="mt-1 text-xl font-bold">
                                        Your Learning Hub
                                    </div>

                                    <p class="mt-2 text-xs leading-5 text-blue-100">
                                        Access your classes, resources and assignments.
                                    </p>

                                    <div class="mt-5 flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-lg bg-white/20"></div>
                                        <div>
                                            <div class="h-2 w-20 rounded bg-white/40"></div>
                                            <div class="mt-1 h-1.5 w-14 rounded bg-white/20"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="text-xs font-medium text-slate-500">
                                        Assignments
                                    </div>

                                    <div class="mt-3 text-2xl font-bold text-slate-900">
                                        04
                                    </div>

                                    <div class="mt-1 text-[11px] text-orange-600">
                                        Pending
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-slate-700">
                                            Upcoming
                                        </span>

                                        <span class="rounded-full bg-blue-50 px-2 py-1 text-[9px] font-medium text-blue-600">
                                            Today
                                        </span>
                                    </div>

                                    <div class="mt-4 space-y-3">

                                        <div class="flex gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                                                </svg>
                                            </div>

                                            <div>
                                                <div class="text-xs font-semibold text-slate-800">
                                                    Mathematics
                                                </div>
                                                <div class="text-[10px] text-slate-400">
                                                    Assignment due Friday
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2h-4l-4 4v-4z"/>
                                                </svg>
                                            </div>

                                            <div>
                                                <div class="text-xs font-semibold text-slate-800">
                                                    Biology Discussion
                                                </div>
                                                <div class="text-[10px] text-slate-400">
                                                    8 replies
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-semibold text-slate-800">
                                                Recent Resources
                                            </div>
                                            <div class="mt-1 text-[10px] text-slate-400">
                                                Learning materials shared by teachers
                                            </div>
                                        </div>

                                        <div class="rounded-lg bg-blue-100 p-2 text-blue-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M7 3h10a2 2 0 012 2v14l-7-3-7 3V5a2 2 0 012-2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid grid-cols-3 gap-2">

                                        <div class="rounded-xl bg-white p-3">
                                            <div class="h-7 w-7 rounded-lg bg-red-100"></div>
                                            <div class="mt-2 h-2 w-14 rounded bg-slate-200"></div>
                                            <div class="mt-1 h-1.5 w-10 rounded bg-slate-100"></div>
                                        </div>

                                        <div class="rounded-xl bg-white p-3">
                                            <div class="h-7 w-7 rounded-lg bg-blue-100"></div>
                                            <div class="mt-2 h-2 w-14 rounded bg-slate-200"></div>
                                            <div class="mt-1 h-1.5 w-10 rounded bg-slate-100"></div>
                                        </div>

                                        <div class="rounded-xl bg-white p-3">
                                            <div class="h-7 w-7 rounded-lg bg-green-100"></div>
                                            <div class="mt-2 h-2 w-14 rounded bg-slate-200"></div>
                                            <div class="mt-1 h-1.5 w-10 rounded bg-slate-100"></div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- Floating notification --}}
                        <div class="floating-card absolute -right-3 top-16 hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-xl sm:block">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-green-100 text-green-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                                <div>
                                    <div class="text-xs font-semibold text-slate-800">
                                        Assignment submitted
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Just now
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="floating-card-delay absolute -left-5 bottom-10 hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-xl sm:block">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2h-4l-4 4v-4z"/>
                                    </svg>
                                </div>

                                <div>
                                    <div class="text-xs font-semibold text-slate-800">
                                        New discussion
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        3 new replies
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             INTRO / BENEFITS
        ========================================================== --}}
        <section id="features" class="border-y border-slate-100 bg-slate-50 py-20 sm:py-24">

            <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="mx-auto max-w-2xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-widest text-blue-600">
                        Everything in one place
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        A better way to manage learning
                    </h2>

                    <p class="mt-4 text-lg leading-8 text-slate-600">
                        eResource connects teachers and students around the work
                        that matters most — learning.
                    </p>
                </div>


                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                    {{-- Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Learning Resources
                        </h3>

                        <p class="mt-2 leading-7 text-slate-600">
                            Keep lesson notes, PDFs, documents, videos and other
                            learning materials organized and easy to access.
                        </p>
                    </div>


                    {{-- Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Assignments
                        </h3>

                        <p class="mt-2 leading-7 text-slate-600">
                            Teachers can create assignments and students can see
                            upcoming work, submit responses and keep track of deadlines.
                        </p>
                    </div>


                    {{-- Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2h-4l-4 4v-4z"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Threaded Discussions
                        </h3>

                        <p class="mt-2 leading-7 text-slate-600">
                            Continue classroom conversations through organized
                            discussion threads where students and teachers can reply.
                        </p>
                    </div>


                    {{-- Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Lesson Planning
                        </h3>

                        <p class="mt-2 leading-7 text-slate-600">
                            Teachers can organize lesson plans and make learning
                            activities easier for students to follow.
                        </p>
                    </div>


                    {{-- Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Secure Access
                        </h3>

                        <p class="mt-2 leading-7 text-slate-600">
                            Sign in using your school Google account and access
                            the classes and resources available to you.
                        </p>
                    </div>


                    {{-- Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-100 text-pink-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M9 17H7a4 4 0 01-4-4V9a4 4 0 014-4h10a4 4 0 014 4v4a4 4 0 01-4 4h-2M9 17v2m6-2v2"/>
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            One Connected Platform
                        </h3>

                        <p class="mt-2 leading-7 text-slate-600">
                            Reduce scattered files and messages by bringing important
                            classroom activities into one platform.
                        </p>
                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             ASSIGNMENTS
        ========================================================== --}}
        <section id="assignments" class="py-20 sm:py-24">

            <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="grid items-center gap-14 lg:grid-cols-2">

                    <div>
                        <div class="text-sm font-semibold uppercase tracking-widest text-orange-600">
                            Assignments
                        </div>

                        <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                            Never lose track of what needs to be done.
                        </h2>

                        <p class="mt-5 text-lg leading-8 text-slate-600">
                            eResource gives students a clear view of their assignments
                            while giving teachers a simple way to distribute and manage work.
                        </p>

                        <div class="mt-8 space-y-5">

                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Clear deadlines
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Students can quickly identify upcoming and overdue work.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Stay organized
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Keep assignments connected to the class where they belong.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900">
                                        Submit work online
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Students can submit their completed work directly through the platform.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>


                    {{-- Assignment preview --}}
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 sm:p-7">

                        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-100 p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-medium text-slate-400">
                                            UPCOMING ASSIGNMENTS
                                        </div>

                                        <div class="mt-1 text-lg font-bold text-slate-900">
                                            Your work
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-orange-100 p-2.5 text-orange-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="divide-y divide-slate-100">

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                                M
                                            </div>

                                            <div>
                                                <div class="font-semibold text-slate-900">
                                                    Mathematics
                                                </div>

                                                <div class="mt-1 text-xs text-slate-500">
                                                    Algebra Practice
                                                </div>
                                            </div>
                                        </div>

                                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-600">
                                            Due Friday
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                                                B
                                            </div>

                                            <div>
                                                <div class="font-semibold text-slate-900">
                                                    Biology
                                                </div>

                                                <div class="mt-1 text-xs text-slate-500">
                                                    Cell Structure
                                                </div>
                                            </div>
                                        </div>

                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600">
                                            Tomorrow
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                                                E
                                            </div>

                                            <div>
                                                <div class="font-semibold text-slate-900">
                                                    English
                                                </div>

                                                <div class="mt-1 text-xs text-slate-500">
                                                    Essay Assignment
                                                </div>
                                            </div>
                                        </div>

                                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-600">
                                            Next week
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             DISCUSSIONS
        ========================================================== --}}
        <section id="discussions" class="bg-slate-950 py-20 text-white sm:py-24">

            <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="grid items-center gap-14 lg:grid-cols-2">

                    {{-- Discussion preview --}}
                    <div class="order-2 lg:order-1">

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4 shadow-2xl sm:p-6">

                            <div class="rounded-2xl bg-white text-slate-900">

                                <div class="border-b border-slate-100 p-5">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-medium text-slate-400">
                                                CLASS DISCUSSION
                                            </div>

                                            <div class="mt-1 text-lg font-bold">
                                                Biology — Ecosystems
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-purple-100 p-2.5 text-purple-600">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-4 2h-4l-4 4v-4z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-5 p-5">

                                    <div class="flex gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">
                                            T
                                        </div>

                                        <div class="flex-1 rounded-2xl rounded-tl-none bg-slate-100 p-4">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold">
                                                    Teacher
                                                </span>

                                                <span class="text-[10px] text-slate-400">
                                                    10:24 AM
                                                </span>
                                            </div>

                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                What examples of ecosystems can you identify
                                                in your local environment?
                                            </p>

                                            <div class="mt-3 text-xs font-medium text-purple-600">
                                                6 replies
                                            </div>
                                        </div>
                                    </div>


                                    <div class="ml-8 flex gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-600">
                                            S
                                        </div>

                                        <div class="flex-1 rounded-2xl rounded-tl-none bg-green-50 p-4">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold">
                                                    Student
                                                </span>

                                                <span class="text-[10px] text-slate-400">
                                                    10:41 AM
                                                </span>
                                            </div>

                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                A pond is an example because different organisms
                                                interact with each other and their environment.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border-t border-slate-100 pt-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-slate-200"></div>

                                            <div class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-400">
                                                Reply to this discussion...
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Text --}}
                    <div class="order-1 lg:order-2">

                        <div class="text-sm font-semibold uppercase tracking-widest text-purple-400">
                            Threaded Conversations
                        </div>

                        <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                            Keep the conversation going beyond the classroom.
                        </h2>

                        <p class="mt-5 text-lg leading-8 text-slate-300">
                            Turn questions, ideas and classroom discussions into
                            organized conversation threads that students and teachers
                            can return to whenever they need.
                        </p>

                        <div class="mt-8 space-y-5">

                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-purple-300">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-4 2h-4l-4 4v-4z"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold">
                                        Organized conversations
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-400">
                                        Keep each discussion focused on a particular topic.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-purple-300">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-4l-4 4v-4H7a2 2 0 01-2-2v-1m12-7V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2l4 4v-4h2a2 2 0 002-2V8z"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold">
                                        Replies and follow-ups
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-400">
                                        Students can respond to teachers and continue discussions.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-purple-300">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-semibold">
                                        Upcoming discussions
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-400">
                                        See active and upcoming conversations from your classes.
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             TEACHERS / STUDENTS
        ========================================================== --}}
        <section class="py-20 sm:py-24">

            <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="mx-auto max-w-2xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-widest text-blue-600">
                        Built for everyone
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        One platform. Two powerful experiences.
                    </h2>
                </div>


                <div class="mt-14 grid gap-6 lg:grid-cols-2">

                    {{-- Teachers --}}
                    <div class="overflow-hidden rounded-3xl border border-blue-100 bg-blue-50">

                        <div class="p-8 sm:p-10">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 15.5c0 1.933-2.686 3.5-6 3.5s-6-1.567-6-3.5c0-1.933.816-3.632 2.09-4.922L12 14z"/>
                                </svg>
                            </div>

                            <h3 class="mt-6 text-2xl font-bold text-slate-900">
                                For Teachers
                            </h3>

                            <p class="mt-3 leading-7 text-slate-600">
                                Create and manage your classes while keeping learning
                                materials and activities organized.
                            </p>

                            <ul class="mt-7 space-y-4">

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-blue-600">✓</span>
                                    Create and manage classes
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-blue-600">✓</span>
                                    Invite students to classes
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-blue-600">✓</span>
                                    Upload learning resources
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-blue-600">✓</span>
                                    Create assignments and lesson plans
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-blue-600">✓</span>
                                    Start class discussions
                                </li>

                            </ul>

                        </div>
                    </div>


                    {{-- Students --}}
                    <div class="overflow-hidden rounded-3xl border border-green-100 bg-green-50">

                        <div class="p-8 sm:p-10">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-600 text-white shadow-lg shadow-green-600/20">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 15.5c0 1.933-2.686 3.5-6 3.5s-6-1.567-6-3.5c0-1.933.816-3.632 2.09-4.922L12 14z"/>
                                </svg>
                            </div>

                            <h3 class="mt-6 text-2xl font-bold text-slate-900">
                                For Students
                            </h3>

                            <p class="mt-3 leading-7 text-slate-600">
                                Find everything you need for your classes and keep
                                up with your learning activities.
                            </p>

                            <ul class="mt-7 space-y-4">

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-green-600">✓</span>
                                    Join your classes
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-green-600">✓</span>
                                    Access learning resources
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-green-600">✓</span>
                                    See upcoming assignments
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-green-600">✓</span>
                                    Submit completed work
                                </li>

                                <li class="flex gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 text-green-600">✓</span>
                                    Participate in class discussions
                                </li>

                            </ul>

                        </div>
                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             HOW IT WORKS
        ========================================================== --}}
        <section id="how-it-works" class="border-y border-slate-100 bg-slate-50 py-20 sm:py-24">

            <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="mx-auto max-w-2xl text-center">
                    <div class="text-sm font-semibold uppercase tracking-widest text-blue-600">
                        Simple by design
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        How eResource works
                    </h2>

                    <p class="mt-4 text-lg leading-8 text-slate-600">
                        Getting started takes only a few simple steps.
                    </p>
                </div>


                <div class="mt-14 grid gap-8 md:grid-cols-4">

                    <div class="relative text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/20">
                            01
                        </div>

                        <h3 class="mt-5 font-bold text-slate-900">
                            Sign in
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Sign in using your school Google account.
                        </p>
                    </div>


                    <div class="relative text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/20">
                            02
                        </div>

                        <h3 class="mt-5 font-bold text-slate-900">
                            Join your class
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Access the classes and learning spaces available to you.
                        </p>
                    </div>


                    <div class="relative text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/20">
                            03
                        </div>

                        <h3 class="mt-5 font-bold text-slate-900">
                            Learn & participate
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Read resources, join discussions and complete your work.
                        </p>
                    </div>


                    <div class="relative text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/20">
                            04
                        </div>

                        <h3 class="mt-5 font-bold text-slate-900">
                            Submit & progress
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Submit assignments and stay up to date with your classes.
                        </p>
                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             SECURITY / ACCESS
        ========================================================== --}}
        <section class="py-20 sm:py-24">

            <div class="mx-auto max-w-5xl px-5 sm:px-6 lg:px-8">

                <div class="rounded-3xl bg-blue-600 p-8 text-white shadow-2xl shadow-blue-600/20 sm:p-12">

                    <div class="grid items-center gap-10 md:grid-cols-[1fr_auto]">

                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-blue-100">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>

                                Secure school access
                            </div>

                            <h2 class="mt-4 text-2xl font-bold sm:text-3xl">
                                Your classes. Your resources. Your learning.
                            </h2>

                            <p class="mt-4 max-w-2xl leading-7 text-blue-100">
                                eResource uses your school Google account to provide
                                access to the learning spaces and resources you are
                                authorized to use.
                            </p>
                        </div>

                        <div>
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-blue-700 shadow-lg transition hover:bg-blue-50">
                                    Open Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-blue-700 shadow-lg transition hover:bg-blue-50">
                                    Sign in
                                </a>
                            @endauth
                        </div>

                    </div>

                </div>
            </div>
        </section>


        {{-- =========================================================
             FINAL CTA
        ========================================================== --}}
        <section class="bg-slate-950 py-20 text-white sm:py-24">

            <div class="mx-auto max-w-4xl px-5 text-center sm:px-6 lg:px-8">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 shadow-xl shadow-blue-600/20">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    </svg>
                </div>

                <h2 class="mt-7 text-3xl font-bold tracking-tight sm:text-4xl">
                    Ready to get started?
                </h2>

                <p class="mx-auto mt-4 max-w-2xl text-lg leading-8 text-slate-300">
                    Sign in with your school Google account and start accessing
                    your classes, resources, assignments and discussions.
                </p>

                <div class="mt-8">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-7 py-3.5 text-sm font-semibold text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700">
                            Go to Dashboard

                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-7 py-3.5 text-sm font-semibold text-white shadow-xl shadow-blue-600/20 transition hover:bg-blue-700">
                            Sign in with Google

                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M21.35 12.27c0-.78-.07-1.53-.2-2.25H12v4.26h5.22a4.46 4.46 0 0 1-1.94 2.93v2.43h3.14c1.84-1.69 2.93-4.18 2.93-7.37z"/>
                                <path fill="currentColor" d="M12 21.99c2.63 0 4.84-.87 6.45-2.35l-3.14-2.43c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.5A9.74 9.74 0 0 0 12 21.99z"/>
                                <path fill="currentColor" d="M6.54 14.1A5.85 5.85 0 0 1 6.23 12c0-.73.13-1.44.31-2.1V7.4H3.3A10 10 0 0 0 2 12c0 1.61.39 3.13 1.3 4.6l3.24-2.5z"/>
                                <path fill="currentColor" d="M12 5.87c1.43 0 2.71.49 3.72 1.46l2.79-2.79C16.84 2.93 14.63 2 12 2a9.74 9.74 0 0 0-8.7 5.4l3.24 2.5C7.31 7.59 9.46 5.87 12 5.87z"/>
                            </svg>
                        </a>
                    @endauth
                </div>

            </div>
        </section>

    </main>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}
    <footer class="border-t border-slate-200 bg-white">

        <div class="mx-auto max-w-7xl px-5 py-10 sm:px-6 lg:px-8">

            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>

                    <div>
                        <div class="font-bold text-slate-900">
                            eResource
                        </div>

                        <div class="text-xs text-slate-500">
                            Learning made simple.
                        </div>
                    </div>

                </div>

                <div class="flex flex-wrap gap-6 text-sm text-slate-500">
                    <a href="#features" class="transition hover:text-blue-600">
                        Features
                    </a>

                    <a href="#assignments" class="transition hover:text-blue-600">
                        Assignments
                    </a>

                    <a href="#discussions" class="transition hover:text-blue-600">
                        Discussions
                    </a>

                    <a href="#how-it-works" class="transition hover:text-blue-600">
                        How it works
                    </a>
                </div>

            </div>

            <div class="mt-8 border-t border-slate-100 pt-6 text-center text-xs text-slate-400 sm:text-left">
                © {{ date('Y') }} eResource. All rights reserved.
            </div>

        </div>

    </footer>

</body>
</html>
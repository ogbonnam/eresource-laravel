<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        @yield('title', 'eResource')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

</head>


<body class="min-h-screen bg-slate-50 text-slate-900">


    {{-- ========================================================= --}}
    {{-- IMPERSONATION BANNER --}}
    {{-- ========================================================= --}}

    @if(session()->has('impersonator_id'))

        <div class="border-b border-amber-300 bg-amber-50">

            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">

                {{-- Banner message --}}

                <div class="flex items-center gap-3">

                    {{-- Warning icon --}}

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">

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
                                d="M12 9v3.75m0 3.75h.008v.008H12v-.008ZM10.29 3.86 1.82 18a1.875 1.875 0 0 0 1.61 2.812h17.14A1.875 1.875 0 0 0 22.18 18L13.71 3.86a1.875 1.875 0 0 0-3.42 0Z"
                            />

                        </svg>

                    </div>


                    {{-- Message --}}

                    <div>

                        <p class="text-sm font-semibold text-amber-900">

                            You are impersonating
                            {{ auth()->user()->name }}

                        </p>

                        <p class="text-xs text-amber-700">

                            You are currently viewing eResource as this user.

                        </p>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- STOP IMPERSONATION --}}
                {{-- ================================================= --}}

                <form
                    method="POST"
                    action="{{ route('impersonation.stop') }}"
                    class="shrink-0"
                >

                    @csrf

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 sm:w-auto"
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
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 12h9m0 0-3-3m3 3-3 3"
                            />

                        </svg>

                        Stop impersonating

                    </button>

                </form>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- TOP NAVIGATION --}}
    {{-- ========================================================= --}}

    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">

        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">


            {{-- ================================================= --}}
            {{-- LOGO --}}
            {{-- ================================================= --}}

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3"
            >

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white"
                >

                    <span class="text-lg font-bold">
                        NH
                    </span>

                </div>

                <div class="hidden sm:block">

                    <div class="text-lg font-semibold tracking-tight">
                        eResource
                    </div>

                    <div class="text-xs text-slate-500">
                        Digital Resource Platform
                    </div>

                </div>

            </a>


            {{-- ================================================= --}}
            {{-- DESKTOP NAVIGATION --}}
            {{-- ================================================= --}}

            <nav class="hidden items-center gap-1 md:flex">


                {{-- ================================================= --}}
                {{-- DASHBOARD --}}
                {{-- ================================================= --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ request()->routeIs('dashboard')
                        ? 'bg-slate-100 text-slate-900'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >

                    Dashboard

                </a>


                {{-- ================================================= --}}
                {{-- STUDENT NAVIGATION --}}
                {{-- ================================================= --}}

                @if(auth()->user()->isStudent())


                    {{-- Courses --}}

                    <a
                        href="{{ route('student.courses.index') }}"
                        class="rounded-lg px-4 py-2 text-sm font-medium transition
                        {{ request()->routeIs('student.courses.*')
                            ? 'bg-slate-100 text-slate-900'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >

                        Courses

                    </a>


                    {{-- Assignments --}}

                    <a
                        href="{{ route('student.assignments.index') }}"
                        class="rounded-lg px-4 py-2 text-sm font-medium transition
                        {{ request()->routeIs('student.assignments.*')
                            ? 'bg-slate-100 text-slate-900'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >

                        Assignments

                    </a>


                    {{-- Progress --}}

                    <span
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-400"
                    >

                        Progress

                    </span>


                {{-- ================================================= --}}
                {{-- TEACHER NAVIGATION --}}
                {{-- ================================================= --}}

                @elseif(auth()->user()->isTeacher())


                    {{-- My Courses --}}

                    <a
                        href="{{ route('teacher.courses.index') }}"
                        class="rounded-lg px-4 py-2 text-sm font-medium transition
                        {{ request()->routeIs('teacher.courses.*')
                            ? 'bg-slate-100 text-slate-900'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >

                        My Courses

                    </a>


                    {{-- ================================================= --}}
                    {{-- COURSE-SPECIFIC NAVIGATION --}}
                    {{-- ================================================= --}}

                    @if(isset($course) && $course->exists)


                        {{-- Assignments --}}

                        <a
                            href="{{ route('teacher.courses.assignments.index', $course) }}"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition
                            {{ request()->routeIs('teacher.courses.assignments.*')
                                ? 'bg-slate-100 text-slate-900'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                        >

                            Assignments

                        </a>


                        {{-- Students --}}

                        <a
                            href="{{ route('teacher.courses.students.index', $course) }}"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition
                            {{ request()->routeIs('teacher.courses.students.*')
                                ? 'bg-slate-100 text-slate-900'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                        >

                            Students

                        </a>


                    @endif


                @endif

            </nav>


            {{-- ================================================= --}}
            {{-- RIGHT SIDE --}}
            {{-- ================================================= --}}

            <div class="flex items-center gap-2">


                {{-- ================================================= --}}
                {{-- NOTIFICATIONS --}}
                {{-- ================================================= --}}

                <button
                    type="button"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                    title="Notifications"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.7"
                        stroke="currentColor"
                        class="h-5 w-5"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 1-5.714 0A8.966 8.966 0 0 1 6 16.5V11a6 6 0 1 1 12 0v5.5a8.966 8.966 0 0 1-3.143.582ZM9 17.5a3 3 0 0 0 6 0"
                        />

                    </svg>


                    {{-- Notification indicator --}}

                    <span
                        class="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500"
                    ></span>

                </button>


                {{-- ================================================= --}}
                {{-- USER MENU --}}
                {{-- ================================================= --}}

                <div class="relative ml-1">


                    {{-- User button --}}

                    <button
                        type="button"
                        id="user-menu-button"
                        class="flex items-center gap-2 rounded-full p-1 transition hover:bg-slate-100"
                    >


                        {{-- Avatar --}}

                        @if(auth()->user()->avatar)

                            <img
                                src="{{ auth()->user()->avatar }}"
                                alt="{{ auth()->user()->name }}"
                                class="h-9 w-9 rounded-full object-cover"
                            >

                        @else

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white"
                            >

                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                            </div>

                        @endif


                        {{-- Name / role --}}

                        <div class="hidden text-left lg:block">

                            <div class="text-sm font-medium">
                                {{ auth()->user()->name }}
                            </div>

                            <div class="text-xs capitalize text-slate-500">
                                {{ auth()->user()->role }}
                            </div>

                        </div>


                        {{-- Chevron --}}

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="hidden h-4 w-4 text-slate-500 lg:block"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m19.5 8.25-7.5 7.5-7.5-7.5"
                            />

                        </svg>

                    </button>


                    {{-- ================================================= --}}
                    {{-- USER DROPDOWN --}}
                    {{-- ================================================= --}}

                    <div
                        id="user-menu"
                        class="absolute right-0 mt-2 hidden w-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                    >


                        {{-- User information --}}

                        <div class="border-b border-slate-100 px-4 py-3">

                            <p class="text-sm font-medium text-slate-900">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="truncate text-xs text-slate-500">
                                {{ auth()->user()->email }}
                            </p>

                        </div>


                        {{-- Menu items --}}

                        <div class="p-1">


                            {{-- Profile --}}

                            <a
                                href="#"
                                class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
                            >

                                Profile

                            </a>


                            {{-- Settings --}}

                            <a
                                href="#"
                                class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
                            >

                                Settings

                            </a>

                        </div>


                        {{-- Logout --}}

                        <div class="border-t border-slate-100 p-1">

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                                >

                                    Sign out

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MOBILE NAVIGATION --}}
        {{-- ========================================================= --}}

        <div class="border-t border-slate-100 md:hidden">

            <nav
                class="mx-auto flex max-w-7xl gap-1 overflow-x-auto px-4 py-2"
            >


                {{-- Dashboard --}}

                <a
                    href="{{ route('dashboard') }}"
                    class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                    {{ request()->routeIs('dashboard')
                        ? 'bg-slate-100 text-slate-900'
                        : 'text-slate-600 hover:bg-slate-50' }}"
                >

                    Dashboard

                </a>


                {{-- ================================================= --}}
                {{-- MOBILE STUDENT --}}
                {{-- ================================================= --}}

                @if(auth()->user()->isStudent())


                    {{-- Courses --}}

                    <a
                        href="{{ route('student.courses.index') }}"
                        class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                        {{ request()->routeIs('student.courses.*')
                            ? 'bg-slate-100 text-slate-900'
                            : 'text-slate-600 hover:bg-slate-50' }}"
                    >

                        My Courses

                    </a>


                    {{-- Assignments --}}

                    <a
                        href="{{ route('student.assignments.index') }}"
                        class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                        {{ request()->routeIs('student.assignments.*')
                            ? 'bg-slate-100 text-slate-900'
                            : 'text-slate-600 hover:bg-slate-50' }}"
                    >

                        Assignments

                    </a>


                    {{-- Progress --}}

                    <span
                        class="whitespace-nowrap rounded-lg px-3 py-2 text-sm text-slate-400"
                    >

                        Progress

                    </span>


                {{-- ================================================= --}}
                {{-- MOBILE TEACHER --}}
                {{-- ================================================= --}}

                @elseif(auth()->user()->isTeacher())


                    {{-- My Courses --}}

                    <a
                        href="{{ route('teacher.courses.index') }}"
                        class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                        {{ request()->routeIs('teacher.courses.*')
                            ? 'bg-slate-100 text-slate-900'
                            : 'text-slate-600 hover:bg-slate-50' }}"
                    >

                        My Courses

                    </a>


                    {{-- Course-specific navigation --}}

                    @if(isset($course) && $course->exists)


                        {{-- Resources --}}

                        <a
                            href="{{ route('teacher.courses.show', $course) }}"
                            class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                            {{ request()->routeIs('teacher.courses.show')
                                ? 'bg-slate-100 text-slate-900'
                                : 'text-slate-600 hover:bg-slate-50' }}"
                        >

                            Resources

                        </a>


                        {{-- Assignments --}}

                        <a
                            href="{{ route('teacher.courses.assignments.index', $course) }}"
                            class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                            {{ request()->routeIs('teacher.courses.assignments.*')
                                ? 'bg-slate-100 text-slate-900'
                                : 'text-slate-600 hover:bg-slate-50' }}"
                        >

                            Assignments

                        </a>


                        {{-- Students --}}

                        <a
                            href="{{ route('teacher.courses.students.index', $course) }}"
                            class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition
                            {{ request()->routeIs('teacher.courses.students.*')
                                ? 'bg-slate-100 text-slate-900'
                                : 'text-slate-600 hover:bg-slate-50' }}"
                        >

                            Students

                        </a>


                    @endif


                @endif

            </nav>

        </div>

    </header>


    {{-- ========================================================= --}}
    {{-- MAIN CONTENT --}}
    {{-- ========================================================= --}}

    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @yield('content')

    </main>


    {{-- ========================================================= --}}
    {{-- USER MENU JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const button =
                document.getElementById('user-menu-button');

            const menu =
                document.getElementById('user-menu');


            if (!button || !menu) {
                return;
            }


            /*
             * Toggle user menu.
             */

            button.addEventListener('click', function (event) {

                event.stopPropagation();

                menu.classList.toggle('hidden');

            });


            /*
             * Close menu when clicking outside.
             */

            document.addEventListener('click', function () {

                menu.classList.add('hidden');

            });


            /*
             * Keep menu open when clicking inside it.
             */

            menu.addEventListener('click', function (event) {

                event.stopPropagation();

            });

        });

    </script>


    @stack('scripts')

</body>

</html>
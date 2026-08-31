<x-filament-panels::page>

    {{-- ========================================================= --}}
    {{-- COURSE HEADER --}}
    {{-- ========================================================= --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ $course->name }}
                    </h1>

                    <x-filament::badge
                        :color="$course->is_active ? 'success' : 'gray'"
                    >
                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                    </x-filament::badge>
                </div>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ $course->subject?->name ?? 'No subject' }}

                    @if($course->schoolClass)
                        <span class="mx-1">•</span>
                        {{ $course->schoolClass->name }}
                    @endif

                    @if($course->code)
                        <span class="mx-1">•</span>
                        {{ $course->code }}
                    @endif
                </p>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- TABS + TAB CONTENT (MOVED TO THE TOP) --}}
    {{-- ========================================================= --}}
    <div
        x-data="{ tab: 'overview' }"
        class="space-y-6"
    >
        {{-- TAB NAVIGATION --}}
        <div class="overflow-x-auto">
            <x-filament::tabs>
                <x-filament::tabs.item
                    alpine-active="tab === 'overview'"
                    x-on:click="tab = 'overview'"
                >
                    Overview
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="tab === 'students'"
                    x-on:click="tab = 'students'"
                >
                    Students
                    <span class="ml-1 text-xs">({{ $students->count() }})</span>
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="tab === 'resources'"
                    x-on:click="tab = 'resources'"
                >
                    Resources
                    <span class="ml-1 text-xs">({{ $resources->count() }})</span>
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="tab === 'assignments'"
                    x-on:click="tab = 'assignments'"
                >
                    Assignments
                    <span class="ml-1 text-xs">({{ $assignments->count() }})</span>
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="tab === 'submissions'"
                    x-on:click="tab = 'submissions'"
                >
                    Submissions
                    <span class="ml-1 text-xs">({{ $totalSubmissions }})</span>
                </x-filament::tabs.item>
                <x-filament::tabs.item 
                    alpine-active="tab === 'analytics'" 
                    x-on:click="tab = 'analytics'" > 
                    Analytics 
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="tab === 'activity'"
                    x-on:click="tab = 'activity'"
                >
                    Activity
                </x-filament::tabs.item>
            </x-filament::tabs>
        </div>


        {{-- ===================================================== --}}
        {{-- TAB CONTENT CONTAINER --}}
        {{-- ===================================================== --}}
        <div>

            
            {{-- ================================================= --}}
            {{-- OVERVIEW TAB --}}
            {{-- ================================================= --}}

            <div
                x-show="tab === 'overview'"
                x-cloak
                class="space-y-6"
            >

                {{-- ================================================= --}}
                {{-- STAT CARDS --}}
                {{-- ================================================= --}}

                <div
                    style="
                        display: flex;
                        flex-direction: row;
                        gap: 20px;
                        width: 100%;
                        margin: 10px 12px;
                        align-items: stretch;
                    "
                    class="overflow-x-auto pb-2"
                >

                    {{-- STUDENTS --}}
                    <div
                        style="
                            flex: 1 1 0%;
                            min-width: 220px;
                            padding: 20px;
                            border-radius: 12px;
                            border: 1px solid rgb(229 231 235);
                            background: white;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                        "
                        class="dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                align-items: flex-start;
                                gap: 15px;
                            "
                        >

                            <div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Students
                                </div>

                                <div class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">
                                    {{ $students->count() }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $activeStudents->count() }} active
                                </div>
                            </div>

                            <div
                                style="
                                    width: 44px;
                                    height: 44px;
                                    min-width: 44px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    border-radius: 10px;
                                    background: rgb(239 246 255);
                                "
                                class="text-primary-600"
                            >
                                <x-heroicon-o-users class="h-6 w-6" />
                            </div>

                        </div>
                    </div>


                    {{-- RESOURCES --}}
                    <div
                        style="
                            flex: 1 1 0%;
                            min-width: 220px;
                            padding: 20px;
                            border-radius: 12px;
                            border: 1px solid rgb(229 231 235);
                            background: white;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                        "
                        class="dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                align-items: flex-start;
                                gap: 15px;
                            "
                        >

                            <div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Resources
                                </div>

                                <div class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">
                                    {{ $resources->count() }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $publishedResources->count() }} published
                                </div>
                            </div>

                            <div
                                style="
                                    width: 44px;
                                    height: 44px;
                                    min-width: 44px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    border-radius: 10px;
                                    background: rgb(240 253 244);
                                "
                                class="text-success-600"
                            >
                                <x-heroicon-o-document-text class="h-6 w-6" />
                            </div>

                        </div>
                    </div>


                    {{-- ASSIGNMENTS --}}
                    <div
                        style="
                            flex: 1 1 0%;
                            min-width: 220px;
                            padding: 20px;
                            border-radius: 12px;
                            border: 1px solid rgb(229 231 235);
                            background: white;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                        "
                        class="dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                align-items: flex-start;
                                gap: 15px;
                            "
                        >

                            <div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Assignments
                                </div>

                                <div class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">
                                    {{ $assignments->count() }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $publishedAssignments->count() }} published
                                </div>
                            </div>

                            <div
                                style="
                                    width: 44px;
                                    height: 44px;
                                    min-width: 44px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    border-radius: 10px;
                                    background: rgb(255 251 235);
                                "
                                class="text-warning-600"
                            >
                                <x-heroicon-o-clipboard-document-list class="h-6 w-6" />
                            </div>

                        </div>
                    </div>


                    {{-- SUBMISSIONS --}}
                    <div
                        style="
                            flex: 1 1 0%;
                            min-width: 220px;
                            padding: 20px;
                            border-radius: 12px;
                            border: 1px solid rgb(229 231 235);
                            background: white;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                        "
                        class="dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                align-items: flex-start;
                                gap: 15px;
                            "
                        >

                            <div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Submissions
                                </div>

                                <div class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">
                                    {{ $totalSubmissions }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $gradedSubmissions->count() }} graded
                                </div>
                            </div>

                            <div
                                style="
                                    width: 44px;
                                    height: 44px;
                                    min-width: 44px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    border-radius: 10px;
                                    background: rgb(239 246 255);
                                "
                                class="text-info-600"
                            >
                                <x-heroicon-o-paper-airplane class="h-6 w-6" />
                            </div>

                        </div>
                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- COURSE INFORMATION + ASSIGNMENT HEALTH --}}
                {{-- ================================================= --}}

                <div
                    style="
                        display: flex;
                        flex-direction: row;
                        gap: 24px;
                        width: 100%;
                        align-items: stretch;
                    "
                    class="overflow-x-auto"
                >

                    {{-- COURSE INFORMATION --}}
                    <div
                        style="
                            flex: 1 1 0%;
                            min-width: 400px;
                        "
                    >
                        <x-filament::section heading="Course Information">

                            <dl class="divide-y divide-gray-100 dark:divide-gray-800">

                                <div class="flex justify-between gap-6 py-3">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        Teacher
                                    </dt>

                                    <dd class="text-right text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $course->teacher?->name ?? 'Not assigned' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-6 py-3">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        Class
                                    </dt>

                                    <dd class="text-right text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $course->schoolClass?->name ?? 'Not assigned' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-6 py-3">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        Subject
                                    </dt>

                                    <dd class="text-right text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $course->subject?->name ?? 'Not assigned' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-6 py-3">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        Course Code
                                    </dt>

                                    <dd class="text-right font-mono text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $course->code ?? '—' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-6 py-3">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        Enrollment Code
                                    </dt>

                                    <dd class="text-right font-mono text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $course->enrollment_code ?? '—' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-6 py-3">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                                        Created
                                    </dt>

                                    <dd class="text-right text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $course->created_at?->format('M d, Y') }}
                                    </dd>
                                </div>

                            </dl>

                        </x-filament::section>
                    </div>


                    {{-- ASSIGNMENT HEALTH --}}
                    <div
                        style="
                            flex: 1 1 0%;
                            min-width: 400px;
                        "
                    >
                        <x-filament::section heading="Assignment Health">

                            <div
                                style="
                                    display: flex;
                                    flex-direction: row;
                                    flex-wrap: wrap;
                                    gap: 16px;
                                "
                            >

                                {{-- PUBLISHED --}}
                                <div
                                    style="
                                        flex: 1 1 calc(50% - 8px);
                                        min-width: 150px;
                                        padding: 16px;
                                        border-radius: 10px;
                                        background: rgb(249 250 251);
                                    "
                                    class="dark:bg-gray-800"
                                >
                                    <div class="text-2xl font-bold text-gray-950 dark:text-white">
                                        {{ $publishedAssignments->count() }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Published
                                    </div>
                                </div>


                                {{-- DRAFT --}}
                                <div
                                    style="
                                        flex: 1 1 calc(50% - 8px);
                                        min-width: 150px;
                                        padding: 16px;
                                        border-radius: 10px;
                                        background: rgb(249 250 251);
                                    "
                                    class="dark:bg-gray-800"
                                >
                                    <div class="text-2xl font-bold text-gray-950 dark:text-white">
                                        {{ $draftAssignments->count() }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Draft
                                    </div>
                                </div>


                                {{-- UPCOMING --}}
                                <div
                                    style="
                                        flex: 1 1 calc(50% - 8px);
                                        min-width: 150px;
                                        padding: 16px;
                                        border-radius: 10px;
                                        background: rgb(249 250 251);
                                    "
                                    class="dark:bg-gray-800"
                                >
                                    <div class="text-2xl font-bold text-gray-950 dark:text-white">
                                        {{ $upcomingAssignments->count() }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Upcoming
                                    </div>
                                </div>


                                {{-- OVERDUE --}}
                                <div
                                    style="
                                        flex: 1 1 calc(50% - 8px);
                                        min-width: 150px;
                                        padding: 16px;
                                        border-radius: 10px;
                                        background: rgb(249 250 251);
                                    "
                                    class="dark:bg-gray-800"
                                >
                                    <div class="text-2xl font-bold text-gray-950 dark:text-white">
                                        {{ $overdueAssignments->count() }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Overdue
                                    </div>
                                </div>

                            </div>

                        </x-filament::section>
                    </div>

                </div>

            </div>



            
            {{-- ===================================================== --}}
            {{-- ANALYTICS --}}
            {{-- ===================================================== --}}

            <div
                x-show="tab === 'analytics'"
                x-cloak
                class="space-y-6"
            >
                @include('filament.resources.courses.pages.analytics', [
                    'analyticsSummary' => $analyticsSummary,
                    'assignmentAnalytics' => $assignmentAnalytics,
                    'topStudents' => $topStudents,
                    'studentsNeedingAttention' => $studentsNeedingAttention,
                    'assignmentsNeedingAttention' => $assignmentsNeedingAttention,
                    'gradedSubmissions' => $gradedSubmissions,
                    'lateSubmissions' => $lateSubmissions,
                    'pendingSubmissions' => $pendingSubmissions,
                    'totalSubmissions' => $totalSubmissions,
                    'submissionTrend' => $submissionTrend,
                    'engagementData' => $engagementData,
                ])
                

            </div>
            


            {{-- ================================================= --}}
            {{-- STUDENTS TAB --}}
            {{-- ================================================= --}}
            <div
                x-show="tab === 'students'"
                x-cloak
            >
                <x-filament::section heading="Enrolled Students">
                    @if($students->count())
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($students as $student)
                                <div class="flex items-center justify-between gap-4 py-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 font-semibold dark:bg-gray-800">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-medium">{{ $student->name }}</div>
                                            <div class="truncate text-xs text-gray-500">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                    <x-filament::badge
                                        :color="($student->pivot->status ?? null) === 'active' ? 'success' : 'gray'"
                                    >
                                        {{ ucfirst($student->pivot->status ?? 'Unknown') }}
                                    </x-filament::badge>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-sm text-gray-500">
                            No students enrolled in this course.
                        </div>
                    @endif
                </x-filament::section>
            </div>


            {{-- ================================================= --}}
            {{-- RESOURCES TAB --}}
            {{-- ================================================= --}}
            <div
                x-show="tab === 'resources'"
                x-cloak
                class="space-y-6"
            >
                @include('filament.resources.courses.pages.resources')
            </div>


            {{-- ================================================= --}}
            {{-- ASSIGNMENTS TAB --}}
            {{-- ================================================= --}}
            <div
                x-show="tab === 'assignments'"
                x-cloak
                class="space-y-6"
            >
                @include('filament.resources.courses.pages.assignments')
            </div>


            {{-- ================================================= --}}
            {{-- SUBMISSIONS TAB --}}
            {{-- ================================================= --}}
            <div
                x-show="tab === 'submissions'"
                x-cloak
                class="space-y-6"
            >
                

                @include('filament.resources.courses.pages.submissions')
            </div>


            {{-- ================================================= --}}
            {{-- ACTIVITY TAB --}}
            {{-- ================================================= --}}
            <div
                x-show="tab === 'activity'"
                x-cloak
            >
                @include('filament.resources.courses.pages.activity')
            </div>

        </div>
    </div>

</x-filament-panels::page>
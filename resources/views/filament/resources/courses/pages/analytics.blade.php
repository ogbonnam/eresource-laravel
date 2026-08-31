<div class="nh-course-analytics">

    {{-- ========================================================= --}}
    {{-- ANALYTICS HEADER --}}
    {{-- ========================================================= --}}

    <div class="nh-analytics-header">
        <h2>Course Analytics</h2>

        <p>
            Monitor student participation, assignment completion,
            submissions, and areas that need attention.
        </p>
    </div>


    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS --}}
    {{-- ========================================================= --}}

    <div class="nh-summary-cards">

        {{-- ACTIVE STUDENTS --}}
        <div class="nh-stat-card">
            <div class="nh-stat-content">

                <div>
                    <div class="nh-stat-label">
                        Active Students
                    </div>

                    <div class="nh-stat-value">
                        {{ $analyticsSummary['active_students'] }}
                    </div>

                    <div class="nh-stat-description">
                        Currently enrolled
                    </div>
                </div>

                <div class="nh-stat-icon nh-icon-primary">
                    <x-heroicon-o-users />
                </div>

            </div>
        </div>


        {{-- SUBMISSION RATE --}}
        <div class="nh-stat-card">

            <div class="nh-stat-content">

                <div>
                    <div class="nh-stat-label">
                        Submission Rate
                    </div>

                    <div class="nh-stat-value">
                        {{ $analyticsSummary['submission_rate'] }}%
                    </div>
                </div>

                <div class="nh-stat-icon nh-icon-success">
                    <x-heroicon-o-check-circle />
                </div>

            </div>

            <div class="nh-progress">
                <div
                    class="nh-progress-bar nh-progress-success"
                    style="width: {{ min(100, $analyticsSummary['submission_rate']) }}%"
                ></div>
            </div>

            <div class="nh-stat-description">
                {{ $analyticsSummary['actual_submissions'] }}
                of
                {{ $analyticsSummary['expected_submissions'] }}
                expected submissions
            </div>

        </div>


        {{-- GRADING RATE --}}
        <div class="nh-stat-card">

            <div class="nh-stat-content">

                <div>
                    <div class="nh-stat-label">
                        Grading Rate
                    </div>

                    <div class="nh-stat-value">
                        {{ $analyticsSummary['grading_rate'] }}%
                    </div>
                </div>

                <div class="nh-stat-icon nh-icon-info">
                    <x-heroicon-o-academic-cap />
                </div>

            </div>

            <div class="nh-stat-description nh-stat-bottom">
                {{ $gradedSubmissions->count() }}
                graded submissions
            </div>

        </div>


        {{-- LATE RATE --}}
        <div class="nh-stat-card">

            <div class="nh-stat-content">

                <div>
                    <div class="nh-stat-label">
                        Late Rate
                    </div>

                    <div class="nh-stat-value">
                        {{ $analyticsSummary['late_rate'] }}%
                    </div>
                </div>

                <div class="nh-stat-icon nh-icon-warning">
                    <x-heroicon-o-clock />
                </div>

            </div>

            <div class="nh-stat-description nh-stat-bottom">
                {{ $lateSubmissions->count() }}
                late submissions
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUBMISSION OVERVIEW --}}
    {{-- ========================================================= --}}

    <div class="nh-panel">

        <div class="nh-panel-header">
            <h3>Submission Overview</h3>

            <p>
                Current course-wide submission status.
            </p>
        </div>


        <div class="nh-overview-cards">

            <div class="nh-overview-card nh-overview-neutral">
                <div class="nh-overview-label">
                    Expected
                </div>

                <div class="nh-overview-value">
                    {{ $analyticsSummary['expected_submissions'] }}
                </div>
            </div>


            <div class="nh-overview-card nh-overview-success">
                <div class="nh-overview-label">
                    Submitted
                </div>

                <div class="nh-overview-value">
                    {{ $analyticsSummary['actual_submissions'] }}
                </div>
            </div>


            <div class="nh-overview-card nh-overview-danger">
                <div class="nh-overview-label">
                    Missing
                </div>

                <div class="nh-overview-value">
                    {{ $analyticsSummary['missing_submissions'] }}
                </div>
            </div>


            <div class="nh-overview-card nh-overview-warning">
                <div class="nh-overview-label">
                    Pending Grading
                </div>

                <div class="nh-overview-value">
                    {{ $pendingSubmissions->count() }}
                </div>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ASSIGNMENT PARTICIPATION --}}
    {{-- ========================================================= --}}

    <div class="nh-panel nh-assignment-panel">

        <div class="nh-panel-header">
            <h3>
                Assignment Participation
            </h3>

            <p>
                See which assignments are being completed and which need attention.
            </p>
        </div>


        @if($assignmentAnalytics->count())

            <div class="nh-assignment-list">

                @foreach($assignmentAnalytics as $assignment)

                    <div class="nh-assignment-row">

                        {{-- ASSIGNMENT --}}
                        <div class="nh-assignment-name">

                            <div class="nh-assignment-title">
                                {{ $assignment['title'] }}
                            </div>

                            @if($assignment['due_at'])
                                <div class="nh-assignment-due">
                                    Due {{ $assignment['due_at']->format('M d, Y H:i') }}
                                </div>
                            @endif

                        </div>


                        {{-- NUMBERS --}}
                        <div class="nh-assignment-stats">

                            <div class="nh-mini-stat nh-mini-neutral">
                                <span>Submitted</span>
                                <strong>
                                    {{ $assignment['submitted'] }}
                                </strong>
                            </div>


                            <div class="nh-mini-stat nh-mini-success">
                                <span>Graded</span>
                                <strong>
                                    {{ $assignment['graded'] }}
                                </strong>
                            </div>


                            <div class="nh-mini-stat nh-mini-warning">
                                <span>Late</span>
                                <strong>
                                    {{ $assignment['late'] }}
                                </strong>
                            </div>


                            <div class="nh-mini-stat nh-mini-danger">
                                <span>Missing</span>
                                <strong>
                                    {{ $assignment['missing'] }}
                                </strong>
                            </div>

                        </div>


                        {{-- COMPLETION --}}
                        <div class="nh-completion">

                            <div class="nh-completion-header">
                                <span>Completion</span>

                                <strong>
                                    {{ $assignment['submission_rate'] }}%
                                </strong>
                            </div>

                            <div class="nh-progress">
                                <div
                                    class="nh-progress-bar nh-progress-primary"
                                    style="width: {{ min(100, $assignment['submission_rate']) }}%"
                                ></div>
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="nh-empty">
                No published assignments available for analytics.
            </div>

        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- STUDENT PARTICIPATION --}}
    {{-- ========================================================= --}}

    <div class="nh-student-columns">

        {{-- STRONG PARTICIPATION --}}
        <div class="nh-panel nh-student-panel">

            <div class="nh-panel-header">
                <h3>
                    Strong Participation
                </h3>

                <p>
                    Students with the highest submission rates.
                </p>
            </div>


            <div class="nh-student-list">

                @forelse($topStudents as $student)

                    <div class="nh-student-row">

                        <div class="nh-student-info">

                            <div class="nh-avatar nh-avatar-success">
                                {{ strtoupper(substr($student['name'], 0, 1)) }}
                            </div>

                            <div class="nh-student-details">

                                <div class="nh-student-name">
                                    {{ $student['name'] }}
                                </div>

                                <div class="nh-student-meta">
                                    {{ $student['submitted'] }} submitted
                                    ·
                                    {{ $student['graded'] }} graded
                                </div>

                            </div>

                        </div>


                        <div class="nh-student-rate nh-rate-success">
                            {{ $student['submission_rate'] }}%
                        </div>

                    </div>

                @empty

                    <div class="nh-empty">
                        No student participation data yet.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- NEEDS ATTENTION --}}
        <div class="nh-panel nh-student-panel">

            <div class="nh-panel-header">
                <h3>
                    Students Needing Attention
                </h3>

                <p>
                    Students with missing work or low participation.
                </p>
            </div>


            <div class="nh-student-list">

                @forelse($studentsNeedingAttention as $student)

                    <div class="nh-student-row">

                        <div class="nh-student-info">

                            <div class="nh-avatar nh-avatar-danger">
                                {{ strtoupper(substr($student['name'], 0, 1)) }}
                            </div>

                            <div class="nh-student-details">

                                <div class="nh-student-name">
                                    {{ $student['name'] }}
                                </div>

                                <div class="nh-student-meta">
                                    {{ $student['missing'] }} missing
                                    ·
                                    {{ $student['late'] }} late
                                </div>

                            </div>

                        </div>


                        <div class="nh-student-rate-block">

                            <div class="nh-student-rate nh-rate-danger">
                                {{ $student['submission_rate'] }}%
                            </div>

                            <div class="nh-rate-label">
                                completion
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="nh-empty nh-empty-success">

                        <x-heroicon-o-check-circle />

                        <strong>
                            Excellent participation
                        </strong>

                        <span>
                            No students currently need attention.
                        </span>

                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ASSIGNMENTS NEEDING ATTENTION --}}
    {{-- ========================================================= --}}

    <div class="nh-panel">

        <div class="nh-panel-header">
            <h3>
                Assignments Needing Attention
            </h3>

            <p>
                Published assignments with the highest number of missing submissions.
            </p>
        </div>


        <div class="nh-attention-list">

            @forelse($assignmentsNeedingAttention as $assignment)

                <div class="nh-attention-row">

                    <div class="nh-attention-info">

                        <div class="nh-assignment-title">
                            {{ $assignment['title'] }}
                        </div>

                        <div class="nh-assignment-due">
                            {{ $assignment['submission_rate'] }}% completion
                        </div>

                    </div>


                    <div class="nh-missing-count">

                        <strong>
                            {{ $assignment['missing'] }}
                        </strong>

                        <span>
                            missing
                        </span>

                    </div>

                </div>

            @empty

                <div class="nh-empty">
                    No published assignments available.
                </div>

            @endforelse

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INTERVENTION --}}
    {{-- ========================================================= --}}

    @if($studentsNeedingAttention->count() > 0)

        <div class="nh-intervention">

            <div class="nh-intervention-icon">
                <x-heroicon-o-exclamation-triangle />
            </div>

            <div class="nh-intervention-content">

                <h3>
                    Intervention Recommended
                </h3>

                <p>
                    {{ $studentsNeedingAttention->count() }}
                    student{{ $studentsNeedingAttention->count() === 1 ? '' : 's' }}
                    currently show{{ $studentsNeedingAttention->count() === 1 ? 's' : '' }}
                    low participation or missing work.
                    Consider reviewing their progress and contacting the relevant
                    teacher or parent where appropriate.
                </p>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ANALYTICS CHARTS --}}
    {{-- ========================================================= --}}

    <div class="nh-analytics-charts">

        {{-- ===================================================== --}}
        {{-- SUBMISSION TREND --}}
        {{-- ===================================================== --}}

        <div class="nh-chart-card nh-chart-trend">

            <div class="nh-chart-header">
                <div>
                    <h3>
                        Submission Trend
                    </h3>

                    <p>
                        Submission activity over the last 7 days.
                    </p>
                </div>
            </div>


            <div class="nh-trend-chart">

                @php
                    $maxTrendValue = max(
                        1,
                        $submissionTrend->max('submitted'),
                        $submissionTrend->max('graded')
                    );
                @endphp


                <div class="nh-chart-y-axis">
                    <span>{{ $maxTrendValue }}</span>
                    <span>{{ round($maxTrendValue / 2) }}</span>
                    <span>0</span>
                </div>


                <div class="nh-chart-area">

                    {{-- GRID --}}
                    <div class="nh-chart-grid">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>


                    {{-- BARS --}}
                    <div class="nh-chart-bars">

                        @foreach($submissionTrend as $day)

                            @php

                                $submittedHeight =
                                    $maxTrendValue > 0
                                        ? ($day['submitted'] / $maxTrendValue) * 100
                                        : 0;

                                $gradedHeight =
                                    $maxTrendValue > 0
                                        ? ($day['graded'] / $maxTrendValue) * 100
                                        : 0;

                            @endphp


                            <div class="nh-chart-column">

                                <div class="nh-bars">

                                    {{-- SUBMITTED --}}
                                    <div
                                        class="nh-bar nh-bar-submitted"
                                        style="height: {{ max(2, $submittedHeight) }}%"
                                        title="Submitted: {{ $day['submitted'] }}"
                                    >
                                        @if($day['submitted'] > 0)
                                            <span>
                                                {{ $day['submitted'] }}
                                            </span>
                                        @endif
                                    </div>


                                    {{-- GRADED --}}
                                    <div
                                        class="nh-bar nh-bar-graded"
                                        style="height: {{ max(2, $gradedHeight) }}%"
                                        title="Graded: {{ $day['graded'] }}"
                                    >
                                        @if($day['graded'] > 0)
                                            <span>
                                                {{ $day['graded'] }}
                                            </span>
                                        @endif
                                    </div>

                                </div>


                                <div class="nh-chart-label">
                                    {{ $day['date'] }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>


            {{-- LEGEND --}}
            <div class="nh-chart-legend">

                <div>
                    <span class="nh-legend-dot nh-dot-submitted"></span>
                    <span>Submitted</span>
                </div>

                <div>
                    <span class="nh-legend-dot nh-dot-graded"></span>
                    <span>Graded</span>
                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- ENGAGEMENT --}}
        {{-- ===================================================== --}}

        <div class="nh-chart-card nh-chart-engagement">

            <div class="nh-chart-header">

                <div>
                    <h3>
                        Engagement
                    </h3>

                    <p>
                        Current submission and grading activity.
                    </p>
                </div>

            </div>


            <div class="nh-engagement-list">

                @forelse($engagementData as $item)

                    @php
                        $percentage =
                            $totalSubmissions > 0
                                ? round(($item['value'] / $totalSubmissions) * 100)
                                : 0;
                    @endphp


                    <div class="nh-engagement-row">

                        <div class="nh-engagement-top">

                            <span>
                                {{ $item['label'] }}
                            </span>

                            <strong>
                                {{ $item['value'] }}
                            </strong>

                        </div>


                        <div class="nh-engagement-track">

                            <div
                                class="nh-engagement-fill"
                                style="width: {{ min(100, $percentage) }}%"
                            ></div>

                        </div>


                        <div class="nh-engagement-percent">
                            {{ $percentage }}%
                        </div>

                    </div>

                @empty

                    <div class="nh-empty">
                        No engagement data available yet.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- ANALYTICS CSS --}}
{{-- ============================================================= --}}

<style>

.nh-course-analytics {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    color: #111827;
    font-family: inherit;
}

.nh-course-analytics *,
.nh-course-analytics *::before,
.nh-course-analytics *::after {
    box-sizing: border-box;
}


/* ================================================================
   HEADER
   ================================================================ */

.nh-course-analytics .nh-analytics-header {
    width: 100%;
    margin: 0 0 24px;
}

.nh-course-analytics .nh-analytics-header h2 {
    margin: 0;
    padding: 0;
    font-size: 20px;
    line-height: 1.4;
    font-weight: 700;
    color: #111827;
}

.nh-course-analytics .nh-analytics-header p {
    margin: 6px 0 0;
    padding: 0;
    font-size: 14px;
    line-height: 1.6;
    color: #6b7280;
}


/* ================================================================
   SUMMARY CARDS
   ================================================================ */

.nh-course-analytics .nh-summary-cards {
    width: 100%;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 20px;
    margin: 0 0 24px;
    padding: 0;
}

.nh-course-analytics .nh-stat-card {
    flex: 1 1 240px;
    min-width: 220px;
    min-height: 150px;
    margin: 0;
    padding: 22px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.nh-course-analytics .nh-stat-content {
    width: 100%;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
}

.nh-course-analytics .nh-stat-label {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 600;
    color: #6b7280;
}

.nh-course-analytics .nh-stat-value {
    margin: 8px 0 0;
    font-size: 32px;
    line-height: 1.1;
    font-weight: 700;
    color: #111827;
}

.nh-course-analytics .nh-stat-description {
    margin: 10px 0 0;
    font-size: 12px;
    line-height: 1.5;
    color: #6b7280;
}

.nh-course-analytics .nh-stat-bottom {
    margin-top: 18px;
}


/* ================================================================
   STAT ICONS
   ================================================================ */

.nh-course-analytics .nh-stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 48px;
    width: 48px;
    height: 48px;
    border-radius: 12px;
}

.nh-course-analytics .nh-stat-icon svg {
    width: 24px;
    height: 24px;
}

.nh-course-analytics .nh-icon-primary {
    color: #4f46e5;
    background: #eef2ff;
}

.nh-course-analytics .nh-icon-success {
    color: #16a34a;
    background: #f0fdf4;
}

.nh-course-analytics .nh-icon-info {
    color: #0284c7;
    background: #f0f9ff;
}

.nh-course-analytics .nh-icon-warning {
    color: #d97706;
    background: #fffbeb;
}


/* ================================================================
   PROGRESS
   ================================================================ */

.nh-course-analytics .nh-progress {
    width: 100%;
    height: 8px;
    margin: 18px 0 0;
    padding: 0;
    overflow: hidden;
    border-radius: 999px;
    background: #f3f4f6;
}

.nh-course-analytics .nh-progress-bar {
    display: block;
    height: 100%;
    min-width: 0;
    margin: 0;
    padding: 0;
    border-radius: 999px;
    transition: width 0.3s ease;
}

.nh-course-analytics .nh-progress-primary {
    background: #6366f1;
}

.nh-course-analytics .nh-progress-success {
    background: #22c55e;
}


/* ================================================================
   PANELS
   ================================================================ */

.nh-course-analytics .nh-panel {
    width: 100%;
    margin: 0 0 24px;
    padding: 0;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.nh-course-analytics .nh-panel-header {
    width: 100%;
    margin: 0;
    padding: 22px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.nh-course-analytics .nh-panel-header h3 {
    margin: 0;
    padding: 0;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 700;
    color: #111827;
}

.nh-course-analytics .nh-panel-header p {
    margin: 5px 0 0;
    padding: 0;
    font-size: 13px;
    line-height: 1.5;
    color: #6b7280;
}


/* ================================================================
   SUBMISSION OVERVIEW
   ================================================================ */

.nh-course-analytics .nh-overview-cards {
    width: 100%;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 16px;
    margin: 0;
    padding: 24px;
}

.nh-course-analytics .nh-overview-card {
    flex: 1 1 180px;
    min-width: 160px;
    margin: 0;
    padding: 18px;
    border-radius: 12px;
}

.nh-course-analytics .nh-overview-label {
    margin: 0;
    font-size: 11px;
    line-height: 1.4;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.nh-course-analytics .nh-overview-value {
    margin: 8px 0 0;
    font-size: 26px;
    line-height: 1.2;
    font-weight: 700;
}

.nh-course-analytics .nh-overview-neutral {
    background: #f9fafb;
    color: #374151;
}

.nh-course-analytics .nh-overview-success {
    background: #f0fdf4;
    color: #15803d;
}

.nh-course-analytics .nh-overview-danger {
    background: #fef2f2;
    color: #dc2626;
}

.nh-course-analytics .nh-overview-warning {
    background: #fffbeb;
    color: #b45309;
}


/* ================================================================
   ASSIGNMENTS
   ================================================================ */

.nh-course-analytics .nh-assignment-list {
    width: 100%;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-assignment-row {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 24px;
    margin: 0;
    padding: 22px 24px;
    border-bottom: 1px solid #f3f4f6;
}

.nh-course-analytics .nh-assignment-row:last-child {
    border-bottom: 0;
}

.nh-course-analytics .nh-assignment-name {
    flex: 0 0 28%;
    min-width: 180px;
}

.nh-course-analytics .nh-assignment-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin: 0;
    padding: 0;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 600;
    color: #111827;
}

.nh-course-analytics .nh-assignment-due {
    margin: 5px 0 0;
    padding: 0;
    font-size: 12px;
    line-height: 1.4;
    color: #6b7280;
}

.nh-course-analytics .nh-assignment-stats {
    flex: 1 1 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    min-width: 0;
}

.nh-course-analytics .nh-mini-stat {
    min-width: 82px;
    margin: 0;
    padding: 10px 13px;
    border-radius: 10px;
}

.nh-course-analytics .nh-mini-stat span {
    display: block;
    margin: 0;
    font-size: 11px;
    line-height: 1.3;
}

.nh-course-analytics .nh-mini-stat strong {
    display: block;
    margin: 4px 0 0;
    font-size: 17px;
    line-height: 1.2;
}

.nh-course-analytics .nh-mini-neutral {
    background: #f9fafb;
    color: #374151;
}

.nh-course-analytics .nh-mini-success {
    background: #f0fdf4;
    color: #15803d;
}

.nh-course-analytics .nh-mini-warning {
    background: #fffbeb;
    color: #b45309;
}

.nh-course-analytics .nh-mini-danger {
    background: #fef2f2;
    color: #dc2626;
}

.nh-course-analytics .nh-completion {
    flex: 0 0 190px;
    width: 190px;
    min-width: 0;
}

.nh-course-analytics .nh-completion-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin: 0;
    font-size: 12px;
}

.nh-course-analytics .nh-completion-header span {
    color: #6b7280;
}

.nh-course-analytics .nh-completion-header strong {
    color: #111827;
}


/* ================================================================
   STUDENTS
   ================================================================ */

.nh-course-analytics .nh-student-columns {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: stretch;
    gap: 24px;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-student-columns .nh-panel {
    flex: 1 1 50%;
    min-width: 0;
}

.nh-course-analytics .nh-student-panel {
    margin-bottom: 24px;
}

.nh-course-analytics .nh-student-list {
    width: 100%;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-student-row {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin: 0;
    padding: 16px 24px;
    border-bottom: 1px solid #f3f4f6;
}

.nh-course-analytics .nh-student-row:last-child {
    border-bottom: 0;
}

.nh-course-analytics .nh-student-info {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.nh-course-analytics .nh-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    margin: 0;
    border-radius: 50%;
    font-size: 14px;
    line-height: 1;
    font-weight: 700;
}

.nh-course-analytics .nh-avatar-success {
    color: #15803d;
    background: #f0fdf4;
}

.nh-course-analytics .nh-avatar-danger {
    color: #dc2626;
    background: #fef2f2;
}

.nh-course-analytics .nh-student-details {
    min-width: 0;
}

.nh-course-analytics .nh-student-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 600;
    color: #111827;
}

.nh-course-analytics .nh-student-meta {
    margin: 3px 0 0;
    font-size: 12px;
    line-height: 1.4;
    color: #6b7280;
}

.nh-course-analytics .nh-student-rate {
    flex-shrink: 0;
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 700;
}

.nh-course-analytics .nh-rate-success {
    color: #16a34a;
}

.nh-course-analytics .nh-rate-danger {
    color: #dc2626;
}

.nh-course-analytics .nh-student-rate-block {
    flex-shrink: 0;
    text-align: right;
}

.nh-course-analytics .nh-rate-label {
    margin: 2px 0 0;
    font-size: 11px;
    line-height: 1.3;
    color: #6b7280;
}


/* ================================================================
   EMPTY STATES
   ================================================================ */

.nh-course-analytics .nh-empty {
    width: 100%;
    margin: 0;
    padding: 40px 24px;
    text-align: center;
    font-size: 13px;
    line-height: 1.5;
    color: #6b7280;
}

.nh-course-analytics .nh-empty-success {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.nh-course-analytics .nh-empty-success svg {
    width: 36px;
    height: 36px;
    margin: 0 0 4px;
    color: #22c55e;
}

.nh-course-analytics .nh-empty-success strong {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    color: #111827;
}

.nh-course-analytics .nh-empty-success span {
    font-size: 12px;
    line-height: 1.4;
    color: #6b7280;
}


/* ================================================================
   ATTENTION
   ================================================================ */

.nh-course-analytics .nh-attention-list {
    width: 100%;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-attention-row {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin: 0;
    padding: 17px 24px;
    border-bottom: 1px solid #f3f4f6;
}

.nh-course-analytics .nh-attention-row:last-child {
    border-bottom: 0;
}

.nh-course-analytics .nh-attention-info {
    min-width: 0;
}

.nh-course-analytics .nh-missing-count {
    flex-shrink: 0;
    text-align: right;
}

.nh-course-analytics .nh-missing-count strong {
    display: block;
    margin: 0;
    font-size: 18px;
    line-height: 1.2;
    font-weight: 700;
    color: #dc2626;
}

.nh-course-analytics .nh-missing-count span {
    display: block;
    margin: 2px 0 0;
    font-size: 11px;
    line-height: 1.3;
    color: #6b7280;
}


/* ================================================================
   INTERVENTION
   ================================================================ */

.nh-course-analytics .nh-intervention {
    width: 100%;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin: 0 0 24px;
    padding: 22px 24px;
    border: 1px solid #fde68a;
    border-radius: 16px;
    background: #fffbeb;
}

.nh-course-analytics .nh-intervention-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 44px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fef3c7;
}

.nh-course-analytics .nh-intervention-icon svg {
    width: 21px;
    height: 21px;
    color: #d97706;
}

.nh-course-analytics .nh-intervention-content {
    min-width: 0;
}

.nh-course-analytics .nh-intervention-content h3 {
    margin: 0;
    padding: 0;
    font-size: 15px;
    line-height: 1.4;
    font-weight: 700;
    color: #92400e;
}

.nh-course-analytics .nh-intervention-content p {
    margin: 5px 0 0;
    padding: 0;
    font-size: 13px;
    line-height: 1.6;
    color: #92400e;
}


/* ================================================================
   ANALYTICS CHARTS
   ================================================================ */

.nh-course-analytics .nh-analytics-charts {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: stretch;
    gap: 24px;
    margin: 0 0 24px;
    padding: 0;
}


/* ================================================================
   CHART CARDS
   ================================================================ */

.nh-course-analytics .nh-chart-card {
    position: relative;
    min-width: 0;
    margin: 0;
    padding: 24px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.04),
        0 4px 12px rgba(0, 0, 0, 0.04);
}

.nh-course-analytics .nh-chart-trend {
    flex: 2 1 0;
    min-width: 0;
}

.nh-course-analytics .nh-chart-engagement {
    flex: 1 1 0;
    min-width: 0;
}


/* ================================================================
   CHART HEADER
   ================================================================ */

.nh-course-analytics .nh-chart-header {
    width: 100%;
    margin: 0 0 24px;
    padding: 0;
}

.nh-course-analytics .nh-chart-header h3 {
    margin: 0;
    padding: 0;
    font-size: 16px;
    line-height: 24px;
    font-weight: 700;
    color: #111827;
}

.nh-course-analytics .nh-chart-header p {
    margin: 5px 0 0;
    padding: 0;
    font-size: 13px;
    line-height: 20px;
    color: #6b7280;
}


/* ================================================================
   SUBMISSION TREND
   ================================================================ */

.nh-course-analytics .nh-trend-chart {
    width: 100%;
    min-height: 260px;
    display: flex;
    align-items: stretch;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-chart-y-axis {
    width: 36px;
    flex: 0 0 36px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    margin: 0;
    padding: 0 8px 25px 0;
    color: #9ca3af;
    font-size: 11px;
    line-height: 1;
    text-align: right;
}

.nh-course-analytics .nh-chart-area {
    position: relative;
    flex: 1 1 auto;
    min-width: 0;
    height: 260px;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-chart-grid {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 25px;
    left: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    margin: 0;
    padding: 0;
    pointer-events: none;
}

.nh-course-analytics .nh-chart-grid span {
    display: block;
    width: 100%;
    height: 0;
    margin: 0;
    padding: 0;
    border-top: 1px dashed #e5e7eb;
}

.nh-course-analytics .nh-chart-bars {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 8px;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-chart-column {
    flex: 1 1 0;
    min-width: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-bars {
    width: 100%;
    height: calc(100% - 25px);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 4px;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-bar {
    position: relative;
    display: block;
    width: 18px;
    min-height: 2px;
    margin: 0;
    padding: 0;
    border-radius: 5px 5px 0 0;
    transition:
        opacity 0.2s ease,
        height 0.3s ease;
}

.nh-course-analytics .nh-bar:hover {
    opacity: 0.75;
}

.nh-course-analytics .nh-bar span {
    position: absolute;
    top: -19px;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
    padding: 0;
    color: #4b5563;
    font-size: 10px;
    line-height: 1;
    font-weight: 600;
    white-space: nowrap;
}

.nh-course-analytics .nh-bar-submitted {
    background: #6366f1;
}

.nh-course-analytics .nh-bar-graded {
    background: #10b981;
}

.nh-course-analytics .nh-chart-label {
    width: 100%;
    height: 25px;
    margin: 0;
    padding: 7px 0 0;
    color: #6b7280;
    font-size: 10px;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
}


/* ================================================================
   CHART LEGEND
   ================================================================ */

.nh-course-analytics .nh-chart-legend {
    width: 100%;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin: 16px 0 0;
    padding: 0;
    color: #6b7280;
    font-size: 12px;
    line-height: 1.4;
}

.nh-course-analytics .nh-chart-legend > div {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-legend-dot {
    display: inline-block;
    flex: 0 0 9px;
    width: 9px;
    height: 9px;
    margin: 0;
    padding: 0;
    border-radius: 50%;
}

.nh-course-analytics .nh-dot-submitted {
    background: #6366f1;
}

.nh-course-analytics .nh-dot-graded {
    background: #10b981;
}


/* ================================================================
   ENGAGEMENT
   ================================================================ */

.nh-course-analytics .nh-engagement-list {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 23px;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-engagement-row {
    width: 100%;
    margin: 0;
    padding: 0;
}

.nh-course-analytics .nh-engagement-top {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 0 0 8px;
    padding: 0;
    color: #4b5563;
    font-size: 13px;
    line-height: 1.4;
}

.nh-course-analytics .nh-engagement-top strong {
    flex-shrink: 0;
    margin: 0;
    padding: 0;
    color: #111827;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 700;
}

.nh-course-analytics .nh-engagement-track {
    width: 100%;
    height: 9px;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border-radius: 999px;
    background: #f3f4f6;
}

.nh-course-analytics .nh-engagement-fill {
    display: block;
    height: 100%;
    min-width: 3px;
    margin: 0;
    padding: 0;
    border-radius: 999px;
    background: #6366f1;
    transition: width 0.3s ease;
}

.nh-course-analytics .nh-engagement-percent {
    margin: 5px 0 0;
    padding: 0;
    color: #9ca3af;
    font-size: 11px;
    line-height: 1.3;
    text-align: right;
}


/* ================================================================
   DARK MODE
   ================================================================ */

.dark .nh-course-analytics {
    color: #f9fafb;
}

.dark .nh-course-analytics .nh-stat-card,
.dark .nh-course-analytics .nh-panel,
.dark .nh-course-analytics .nh-chart-card {
    border-color: #374151;
    background: #111827;
}

.dark .nh-course-analytics .nh-analytics-header h2,
.dark .nh-course-analytics .nh-stat-value,
.dark .nh-course-analytics .nh-panel-header h3,
.dark .nh-course-analytics .nh-assignment-title,
.dark .nh-course-analytics .nh-student-name,
.dark .nh-course-analytics .nh-completion-header strong,
.dark .nh-course-analytics .nh-empty-success strong,
.dark .nh-course-analytics .nh-chart-header h3,
.dark .nh-course-analytics .nh-engagement-top strong {
    color: #f9fafb;
}

.dark .nh-course-analytics .nh-analytics-header p,
.dark .nh-course-analytics .nh-stat-label,
.dark .nh-course-analytics .nh-stat-description,
.dark .nh-course-analytics .nh-panel-header p,
.dark .nh-course-analytics .nh-assignment-due,
.dark .nh-course-analytics .nh-student-meta,
.dark .nh-course-analytics .nh-rate-label,
.dark .nh-course-analytics .nh-completion-header span,
.dark .nh-course-analytics .nh-empty,
.dark .nh-course-analytics .nh-chart-header p,
.dark .nh-course-analytics .nh-chart-legend,
.dark .nh-course-analytics .nh-engagement-top {
    color: #9ca3af;
}

.dark .nh-course-analytics .nh-panel-header,
.dark .nh-course-analytics .nh-assignment-row,
.dark .nh-course-analytics .nh-student-row,
.dark .nh-course-analytics .nh-attention-row {
    border-color: #374151;
}

.dark .nh-course-analytics .nh-overview-neutral,
.dark .nh-course-analytics .nh-mini-neutral {
    background: #1f2937;
    color: #d1d5db;
}

.dark .nh-course-analytics .nh-overview-success,
.dark .nh-course-analytics .nh-mini-success,
.dark .nh-course-analytics .nh-avatar-success {
    background: #052e16;
    color: #86efac;
}

.dark .nh-course-analytics .nh-overview-danger,
.dark .nh-course-analytics .nh-mini-danger,
.dark .nh-course-analytics .nh-avatar-danger {
    background: #450a0a;
    color: #fca5a5;
}

.dark .nh-course-analytics .nh-overview-warning,
.dark .nh-course-analytics .nh-mini-warning {
    background: #451a03;
    color: #fcd34d;
}

.dark .nh-course-analytics .nh-icon-primary {
    background: #1e1b4b;
}

.dark .nh-course-analytics .nh-icon-success {
    background: #052e16;
}

.dark .nh-course-analytics .nh-icon-info {
    background: #082f49;
}

.dark .nh-course-analytics .nh-icon-warning {
    background: #451a03;
}

.dark .nh-course-analytics .nh-progress {
    background: #374151;
}

.dark .nh-course-analytics .nh-chart-grid span {
    border-color: #374151;
}

.dark .nh-course-analytics .nh-chart-y-axis,
.dark .nh-course-analytics .nh-chart-label {
    color: #9ca3af;
}

.dark .nh-course-analytics .nh-bar span {
    color: #d1d5db;
}

.dark .nh-course-analytics .nh-engagement-track {
    background: #374151;
}

.dark .nh-course-analytics .nh-intervention {
    border-color: #78350f;
    background: #451a03;
}

.dark .nh-course-analytics .nh-intervention-icon {
    background: #78350f;
}

.dark .nh-course-analytics .nh-intervention-content h3,
.dark .nh-course-analytics .nh-intervention-content p {
    color: #fde68a;
}


/* ================================================================
   TABLET
   ================================================================ */

@media (max-width: 1100px) {

    .nh-course-analytics .nh-assignment-row {
        flex-wrap: wrap;
    }

    .nh-course-analytics .nh-assignment-name {
        flex: 1 1 100%;
        min-width: 0;
    }

    .nh-course-analytics .nh-assignment-stats {
        flex: 1 1 auto;
    }

    .nh-course-analytics .nh-completion {
        flex: 1 1 180px;
        width: auto;
    }

    .nh-course-analytics .nh-analytics-charts {
        flex-direction: column;
    }

    .nh-course-analytics .nh-chart-trend,
    .nh-course-analytics .nh-chart-engagement {
        width: 100%;
        flex: none;
    }
}


/* ================================================================
   SMALL TABLET
   ================================================================ */

@media (max-width: 850px) {

    .nh-course-analytics .nh-student-columns {
        flex-direction: column;
        gap: 0;
    }

    .nh-course-analytics .nh-student-columns .nh-panel {
        width: 100%;
        flex: none;
    }
}


/* ================================================================
   MOBILE
   ================================================================ */

@media (max-width: 650px) {

    .nh-course-analytics .nh-summary-cards {
        flex-direction: column;
        gap: 14px;
    }

    .nh-course-analytics .nh-stat-card {
        width: 100%;
        min-width: 0;
    }

    .nh-course-analytics .nh-overview-cards {
        flex-direction: column;
        gap: 12px;
        padding: 18px;
    }

    .nh-course-analytics .nh-overview-card {
        width: 100%;
        min-width: 0;
    }

    .nh-course-analytics .nh-panel-header {
        padding: 18px;
    }

    .nh-course-analytics .nh-assignment-row {
        padding: 18px;
    }

    .nh-course-analytics .nh-assignment-stats {
        width: 100%;
    }

    .nh-course-analytics .nh-mini-stat {
        flex: 1 1 calc(50% - 10px);
    }

    .nh-course-analytics .nh-completion {
        width: 100%;
        flex: 1 1 100%;
    }

    .nh-course-analytics .nh-student-row {
        padding: 14px 18px;
    }

    .nh-course-analytics .nh-attention-row {
        padding: 15px 18px;
    }

    .nh-course-analytics .nh-intervention {
        padding: 18px;
    }

    .nh-course-analytics .nh-chart-card {
        padding: 18px;
        border-radius: 12px;
    }

    .nh-course-analytics .nh-chart-bars {
        gap: 4px;
    }

    .nh-course-analytics .nh-bar {
        width: 12px;
    }

    .nh-course-analytics .nh-chart-label {
        font-size: 9px;
    }

    .nh-course-analytics .nh-chart-y-axis {
        width: 30px;
        flex-basis: 30px;
        padding-right: 6px;
    }
}


/* ================================================================
   VERY SMALL MOBILE
   ================================================================ */

@media (max-width: 420px) {

    .nh-course-analytics .nh-stat-card {
        padding: 18px;
    }

    .nh-course-analytics .nh-stat-value {
        font-size: 28px;
    }

    .nh-course-analytics .nh-chart-card {
        padding: 15px;
    }

    .nh-course-analytics .nh-bars {
        gap: 2px;
    }

    .nh-course-analytics .nh-bar {
        width: 9px;
    }

    .nh-course-analytics .nh-chart-label {
        font-size: 8px;
    }
}

</style>
{{-- ========================================================= --}}
{{-- SUBMISSIONS --}}
{{-- ========================================================= --}}

<div class="nh-submissions">

    {{-- ===================================================== --}}
    {{-- SUBMISSION SUMMARY --}}
    {{-- ===================================================== --}}

    <div class="nh-submission-stats">

        @foreach([
            ['label' => 'Total', 'value' => $totalSubmissions],
            ['label' => 'Submitted', 'value' => $submittedSubmissions->count()],
            ['label' => 'Graded', 'value' => $gradedSubmissions->count()],
            ['label' => 'Late', 'value' => $lateSubmissions->count()],
            ['label' => 'Pending grading', 'value' => $pendingSubmissions->count()],
        ] as $stat)

            <div class="nh-submission-stat">

                <div class="nh-submission-stat-value">
                    {{ $stat['value'] }}
                </div>

                <div class="nh-submission-stat-label">
                    {{ $stat['label'] }}
                </div>

            </div>

        @endforeach

    </div>


    {{-- ===================================================== --}}
    {{-- RECENT SUBMISSIONS --}}
    {{-- ===================================================== --}}

    <div class="nh-submission-panel">

        <div class="nh-submission-panel-header">

            <div>
                <h3>
                    Recent Submissions
                </h3>

                <p>
                    The latest student submissions recorded in this course.
                </p>
            </div>

        </div>


        <div class="nh-submission-list">

            @forelse($submissions->take(30) as $submission)

                <div class="nh-submission-row">

                    {{-- STUDENT / ASSIGNMENT --}}
                    <div class="nh-submission-info">

                        <div class="nh-submission-student">
                            {{ $submission->student?->name ?? 'Unknown student' }}
                        </div>

                        <div class="nh-submission-meta">

                            {{ $submission->assignment?->title ?? 'Unknown assignment' }}

                            <span class="nh-submission-separator">
                                ·
                            </span>

                            {{ ($submission->submitted_at ?? $submission->created_at)?->diffForHumans() }}

                        </div>

                    </div>


                    {{-- STATUS --}}
                    <div class="nh-submission-status">

                        @if($submission->is_late)

                            <span class="nh-submission-badge nh-badge-danger">
                                Late
                            </span>

                        @endif


                        @if($submission->status === 'graded')

                            <span class="nh-submission-badge nh-badge-success">
                                Graded
                            </span>

                        @elseif($submission->status === 'submitted')

                            <span class="nh-submission-badge nh-badge-warning">
                                Submitted
                            </span>

                        @else

                            <span class="nh-submission-badge nh-badge-neutral">
                                {{ ucfirst($submission->status) }}
                            </span>

                        @endif

                    </div>

                </div>

            @empty

                <div class="nh-submission-empty">

                    <div class="nh-submission-empty-icon">
                        <x-heroicon-o-inbox />
                    </div>

                    <div class="nh-submission-empty-title">
                        No submissions yet
                    </div>

                    <div class="nh-submission-empty-description">
                        Student submissions will appear here once they are received.
                    </div>

                </div>

            @endforelse

        </div>

    </div>

</div>


<style>
/*
|--------------------------------------------------------------------------
| Noble Hall Course Submissions
|--------------------------------------------------------------------------
| Completely isolated from Filament / Tailwind.
|--------------------------------------------------------------------------
*/

.nh-submissions {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    color: #111827;
    font-family: inherit;
    box-sizing: border-box;
}

.nh-submissions *,
.nh-submissions *::before,
.nh-submissions *::after {
    box-sizing: border-box;
}


/* ================================================================
   SUMMARY STATISTICS
   ================================================================ */

.nh-submissions .nh-submission-stats {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 20px;
    margin: 0 0 24px 0;
    padding: 0;
}

.nh-submissions .nh-submission-stat {
    min-width: 0;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin: 0;
    padding: 22px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.04),
        0 4px 12px rgba(0, 0, 0, 0.04);
}

.nh-submissions .nh-submission-stat-value {
    margin: 0;
    padding: 0;
    font-size: 30px;
    line-height: 1.1;
    font-weight: 700;
    color: #111827;
}

.nh-submissions .nh-submission-stat-label {
    margin: 8px 0 0 0;
    padding: 0;
    font-size: 12px;
    line-height: 1.4;
    font-weight: 600;
    color: #6b7280;
}


/* ================================================================
   MAIN PANEL
   ================================================================ */

.nh-submissions .nh-submission-panel {
    width: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow:
        0 1px 2px rgba(0, 0, 0, 0.04),
        0 4px 12px rgba(0, 0, 0, 0.04);
}


/* ================================================================
   PANEL HEADER
   ================================================================ */

.nh-submissions .nh-submission-panel-header {
    width: 100%;
    margin: 0;
    padding: 22px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.nh-submissions .nh-submission-panel-header h3 {
    margin: 0;
    padding: 0;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 700;
    color: #111827;
}

.nh-submissions .nh-submission-panel-header p {
    margin: 5px 0 0 0;
    padding: 0;
    font-size: 13px;
    line-height: 1.5;
    color: #6b7280;
}


/* ================================================================
   SUBMISSION LIST
   ================================================================ */

.nh-submissions .nh-submission-list {
    width: 100%;
    margin: 0;
    padding: 0;
}


/* ================================================================
   SUBMISSION ROW
   ================================================================ */

.nh-submissions .nh-submission-row {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin: 0;
    padding: 18px 24px;
    border-bottom: 1px solid #f3f4f6;
}

.nh-submissions .nh-submission-row:last-child {
    border-bottom: 0;
}


/* ================================================================
   SUBMISSION INFORMATION
   ================================================================ */

.nh-submissions .nh-submission-info {
    flex: 1 1 auto;
    min-width: 0;
}

.nh-submissions .nh-submission-student {
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

.nh-submissions .nh-submission-meta {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin: 4px 0 0 0;
    padding: 0;
    font-size: 12px;
    line-height: 1.4;
    color: #6b7280;
}

.nh-submissions .nh-submission-separator {
    margin: 0 4px;
    color: #9ca3af;
}


/* ================================================================
   STATUS
   ================================================================ */

.nh-submissions .nh-submission-status {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0;
    padding: 0;
}

.nh-submissions .nh-submission-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 26px;
    margin: 0;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    line-height: 1.2;
    font-weight: 600;
    white-space: nowrap;
}


/* ================================================================
   BADGE COLORS
   ================================================================ */

.nh-submissions .nh-badge-danger {
    background: #fef2f2;
    color: #dc2626;
}

.nh-submissions .nh-badge-success {
    background: #f0fdf4;
    color: #15803d;
}

.nh-submissions .nh-badge-warning {
    background: #fffbeb;
    color: #b45309;
}

.nh-submissions .nh-badge-neutral {
    background: #f3f4f6;
    color: #4b5563;
}


/* ================================================================
   EMPTY STATE
   ================================================================ */

.nh-submissions .nh-submission-empty {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 60px 24px;
    text-align: center;
}

.nh-submissions .nh-submission-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    margin: 0 0 12px 0;
    border-radius: 12px;
    background: #f3f4f6;
    color: #6b7280;
}

.nh-submissions .nh-submission-empty-icon svg {
    width: 22px;
    height: 22px;
}

.nh-submissions .nh-submission-empty-title {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 600;
    color: #374151;
}

.nh-submissions .nh-submission-empty-description {
    max-width: 420px;
    margin: 5px 0 0 0;
    font-size: 12px;
    line-height: 1.5;
    color: #9ca3af;
}


/* ================================================================
   DARK MODE
   ================================================================ */

.dark .nh-submissions {
    color: #f9fafb;
}

.dark .nh-submissions .nh-submission-stat,
.dark .nh-submissions .nh-submission-panel {
    border-color: #374151;
    background: #111827;
}

.dark .nh-submissions .nh-submission-stat-value,
.dark .nh-submissions .nh-submission-panel-header h3,
.dark .nh-submissions .nh-submission-student {
    color: #f9fafb;
}

.dark .nh-submissions .nh-submission-stat-label,
.dark .nh-submissions .nh-submission-panel-header p,
.dark .nh-submissions .nh-submission-meta {
    color: #9ca3af;
}

.dark .nh-submissions .nh-submission-panel-header,
.dark .nh-submissions .nh-submission-row {
    border-color: #374151;
}

.dark .nh-submissions .nh-badge-danger {
    background: #450a0a;
    color: #fca5a5;
}

.dark .nh-submissions .nh-badge-success {
    background: #052e16;
    color: #86efac;
}

.dark .nh-submissions .nh-badge-warning {
    background: #451a03;
    color: #fcd34d;
}

.dark .nh-submissions .nh-badge-neutral {
    background: #1f2937;
    color: #d1d5db;
}

.dark .nh-submissions .nh-submission-empty-icon {
    background: #1f2937;
    color: #9ca3af;
}

.dark .nh-submissions .nh-submission-empty-title {
    color: #d1d5db;
}

.dark .nh-submissions .nh-submission-empty-description {
    color: #9ca3af;
}


/* ================================================================
   TABLET
   ================================================================ */

@media (max-width: 1100px) {

    .nh-submissions .nh-submission-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

}


/* ================================================================
   SMALL TABLET
   ================================================================ */

@media (max-width: 750px) {

    .nh-submissions .nh-submission-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .nh-submissions .nh-submission-row {
        align-items: flex-start;
    }

}


/* ================================================================
   MOBILE
   ================================================================ */

@media (max-width: 550px) {

    .nh-submissions .nh-submission-stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .nh-submissions .nh-submission-stat {
        min-height: 100px;
        padding: 18px;
    }

    .nh-submissions .nh-submission-stat-value {
        font-size: 28px;
    }

    .nh-submissions .nh-submission-panel-header {
        padding: 18px;
    }

    .nh-submissions .nh-submission-row {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 16px 18px;
    }

    .nh-submissions .nh-submission-status {
        justify-content: flex-start;
    }

    .nh-submissions .nh-submission-meta {
        white-space: normal;
    }

    .nh-submissions .nh-submission-empty {
        padding: 48px 18px;
    }

}
</style>
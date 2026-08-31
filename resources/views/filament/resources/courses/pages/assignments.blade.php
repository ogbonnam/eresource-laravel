<div class="nh-assignments">

```
{{-- ========================================================= --}}
{{-- ASSIGNMENT SUMMARY --}}
{{-- ========================================================= --}}

<div class="nh-assignment-stats">

    @foreach([
        ['label' => 'Total', 'value' => $assignments->count()],
        ['label' => 'Published', 'value' => $publishedAssignments->count()],
        ['label' => 'Upcoming', 'value' => $upcomingAssignments->count()],
        ['label' => 'Overdue', 'value' => $overdueAssignments->count()],
    ] as $stat)

        <div class="nh-assignment-stat">

            <div class="nh-assignment-stat-value">
                {{ $stat['value'] }}
            </div>

            <div class="nh-assignment-stat-label">
                {{ $stat['label'] }}
            </div>

        </div>

    @endforeach

</div>


{{-- ========================================================= --}}
{{-- ASSIGNMENTS LIST --}}
{{-- ========================================================= --}}

<div class="nh-assignment-panel">

    <div class="nh-assignment-panel-header">

        <div>
            <h3>
                Assignments
            </h3>

            <p>
                All assignments created in this course.
            </p>
        </div>

    </div>


    <div class="nh-assignment-list">

        @forelse($assignments as $assignment)

            <div class="nh-assignment-row">

                {{-- ASSIGNMENT INFORMATION --}}
                <div class="nh-assignment-main">

                    <div class="nh-assignment-title">
                        {{ $assignment->title }}
                    </div>

                    <div class="nh-assignment-meta">

                        Created
                        {{ $assignment->created_at?->diffForHumans() }}

                        @if($assignment->due_at)

                            <span class="nh-assignment-separator">
                                ·
                            </span>

                            Due
                            {{ $assignment->due_at->format('M d, Y H:i') }}

                        @endif

                    </div>

                </div>


                {{-- STATUS --}}
                <div class="nh-assignment-status">

                    @if($assignment->is_published)

                        <span class="nh-assignment-badge nh-badge-success">
                            Published
                        </span>

                    @else

                        <span class="nh-assignment-badge nh-badge-neutral">
                            Draft
                        </span>

                    @endif

                </div>


                {{-- STATISTICS --}}
                <div class="nh-assignment-metrics">

                    <div class="nh-assignment-metric">

                        <span class="nh-metric-label">
                            Submissions
                        </span>

                        <strong>
                            {{ $assignment->submissions->count() }}
                        </strong>

                    </div>


                    <div class="nh-assignment-metric nh-metric-success">

                        <span class="nh-metric-label">
                            Graded
                        </span>

                        <strong>
                            {{ $assignment->submissions->where('status', 'graded')->count() }}
                        </strong>

                    </div>


                    <div class="nh-assignment-metric nh-metric-warning">

                        <span class="nh-metric-label">
                            Late
                        </span>

                        <strong>
                            {{ $assignment->submissions->where('is_late', true)->count() }}
                        </strong>

                    </div>

                </div>

            </div>

        @empty

            <div class="nh-assignment-empty">

                <div class="nh-assignment-empty-icon">
                    <x-heroicon-o-clipboard-document-list />
                </div>

                <div class="nh-assignment-empty-title">
                    No assignments created yet
                </div>

                <div class="nh-assignment-empty-description">
                    Assignments created for this course will appear here.
                </div>

            </div>

        @endforelse

    </div>

</div>
```

</div>

<style>
/*
|--------------------------------------------------------------------------
| Noble Hall Course Assignments
|--------------------------------------------------------------------------
| Completely isolated from Filament / Tailwind.
|--------------------------------------------------------------------------
*/

.nh-assignments {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    color: #111827;
    font-family: inherit;
    box-sizing: border-box;
}

.nh-assignments *,
.nh-assignments *::before,
.nh-assignments *::after {
    box-sizing: border-box;
}


/* ================================================================
   SUMMARY STATISTICS
   ================================================================ */

.nh-assignments .nh-assignment-stats {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
    margin: 0 0 24px 0;
    padding: 0;
}

.nh-assignments .nh-assignment-stat {
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

.nh-assignments .nh-assignment-stat-value {
    margin: 0;
    padding: 0;
    font-size: 30px;
    line-height: 1.1;
    font-weight: 700;
    color: #111827;
}

.nh-assignments .nh-assignment-stat-label {
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

.nh-assignments .nh-assignment-panel {
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

.nh-assignments .nh-assignment-panel-header {
    width: 100%;
    margin: 0;
    padding: 22px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.nh-assignments .nh-assignment-panel-header h3 {
    margin: 0;
    padding: 0;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 700;
    color: #111827;
}

.nh-assignments .nh-assignment-panel-header p {
    margin: 5px 0 0 0;
    padding: 0;
    font-size: 13px;
    line-height: 1.5;
    color: #6b7280;
}


/* ================================================================
   ASSIGNMENT LIST
   ================================================================ */

.nh-assignments .nh-assignment-list {
    width: 100%;
    margin: 0;
    padding: 0;
}


/* ================================================================
   ASSIGNMENT ROW
   ================================================================ */

.nh-assignments .nh-assignment-row {
    width: 100%;
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto auto;
    align-items: center;
    gap: 24px;
    margin: 0;
    padding: 20px 24px;
    border-bottom: 1px solid #f3f4f6;
}

.nh-assignments .nh-assignment-row:last-child {
    border-bottom: 0;
}


/* ================================================================
   ASSIGNMENT INFORMATION
   ================================================================ */

.nh-assignments .nh-assignment-main {
    min-width: 0;
}

.nh-assignments .nh-assignment-title {
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

.nh-assignments .nh-assignment-meta {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin: 5px 0 0 0;
    padding: 0;
    font-size: 12px;
    line-height: 1.4;
    color: #6b7280;
}

.nh-assignments .nh-assignment-separator {
    margin: 0 4px;
    color: #9ca3af;
}


/* ================================================================
   STATUS
   ================================================================ */

.nh-assignments .nh-assignment-status {
    flex: 0 0 auto;
    margin: 0;
    padding: 0;
}

.nh-assignments .nh-assignment-badge {
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

.nh-assignments .nh-badge-success {
    background: #f0fdf4;
    color: #15803d;
}

.nh-assignments .nh-badge-neutral {
    background: #f3f4f6;
    color: #4b5563;
}


/* ================================================================
   METRICS
   ================================================================ */

.nh-assignments .nh-assignment-metrics {
    display: flex;
    align-items: stretch;
    gap: 10px;
    margin: 0;
    padding: 0;
}

.nh-assignments .nh-assignment-metric {
    min-width: 82px;
    margin: 0;
    padding: 9px 12px;
    border-radius: 10px;
    background: #f9fafb;
    color: #374151;
    text-align: center;
}

.nh-assignments .nh-assignment-metric .nh-metric-label {
    display: block;
    margin: 0;
    font-size: 10px;
    line-height: 1.3;
    color: #6b7280;
}

.nh-assignments .nh-assignment-metric strong {
    display: block;
    margin: 4px 0 0 0;
    font-size: 16px;
    line-height: 1.2;
    font-weight: 700;
    color: #111827;
}

.nh-assignments .nh-metric-success {
    background: #f0fdf4;
}

.nh-assignments .nh-metric-success strong {
    color: #15803d;
}

.nh-assignments .nh-metric-warning {
    background: #fffbeb;
}

.nh-assignments .nh-metric-warning strong {
    color: #b45309;
}


/* ================================================================
   EMPTY STATE
   ================================================================ */

.nh-assignments .nh-assignment-empty {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 60px 24px;
    text-align: center;
}

.nh-assignments .nh-assignment-empty-icon {
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

.nh-assignments .nh-assignment-empty-icon svg {
    width: 22px;
    height: 22px;
}

.nh-assignments .nh-assignment-empty-title {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 600;
    color: #374151;
}

.nh-assignments .nh-assignment-empty-description {
    max-width: 420px;
    margin: 5px 0 0 0;
    font-size: 12px;
    line-height: 1.5;
    color: #9ca3af;
}


/* ================================================================
   DARK MODE
   ================================================================ */

.dark .nh-assignments {
    color: #f9fafb;
}

.dark .nh-assignments .nh-assignment-stat,
.dark .nh-assignments .nh-assignment-panel {
    border-color: #374151;
    background: #111827;
}

.dark .nh-assignments .nh-assignment-stat-value,
.dark .nh-assignments .nh-assignment-panel-header h3,
.dark .nh-assignments .nh-assignment-title,
.dark .nh-assignments .nh-assignment-metric strong {
    color: #f9fafb;
}

.dark .nh-assignments .nh-assignment-stat-label,
.dark .nh-assignments .nh-assignment-panel-header p,
.dark .nh-assignments .nh-assignment-meta,
.dark .nh-assignments .nh-assignment-metric .nh-metric-label {
    color: #9ca3af;
}

.dark .nh-assignments .nh-assignment-panel-header,
.dark .nh-assignments .nh-assignment-row {
    border-color: #374151;
}

.dark .nh-assignments .nh-badge-success,
.dark .nh-assignments .nh-metric-success {
    background: #052e16;
}

.dark .nh-assignments .nh-badge-success {
    color: #86efac;
}

.dark .nh-assignments .nh-metric-success strong {
    color: #86efac;
}

.dark .nh-assignments .nh-badge-neutral,
.dark .nh-assignments .nh-assignment-metric {
    background: #1f2937;
}

.dark .nh-assignments .nh-badge-neutral {
    color: #d1d5db;
}

.dark .nh-assignments .nh-metric-warning {
    background: #451a03;
}

.dark .nh-assignments .nh-metric-warning strong {
    color: #fcd34d;
}

.dark .nh-assignments .nh-assignment-empty-icon {
    background: #1f2937;
    color: #9ca3af;
}

.dark .nh-assignments .nh-assignment-empty-title {
    color: #d1d5db;
}

.dark .nh-assignments .nh-assignment-empty-description {
    color: #9ca3af;
}


/* ================================================================
   TABLET
   ================================================================ */

@media (max-width: 1050px) {

    .nh-assignments .nh-assignment-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .nh-assignments .nh-assignment-row {
        grid-template-columns: minmax(0, 1fr) auto;
    }

    .nh-assignments .nh-assignment-metrics {
        grid-column: 1 / -1;
    }

}


/* ================================================================
   MOBILE
   ================================================================ */

@media (max-width: 600px) {

    .nh-assignments .nh-assignment-stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .nh-assignments .nh-assignment-stat {
        min-height: 100px;
        padding: 18px;
    }

    .nh-assignments .nh-assignment-stat-value {
        font-size: 28px;
    }

    .nh-assignments .nh-assignment-panel-header {
        padding: 18px;
    }

    .nh-assignments .nh-assignment-row {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 17px 18px;
    }

    .nh-assignments .nh-assignment-status {
        justify-self: start;
    }

    .nh-assignments .nh-assignment-metrics {
        width: 100%;
        grid-column: auto;
    }

    .nh-assignments .nh-assignment-metric {
        flex: 1 1 0;
        min-width: 0;
    }

    .nh-assignments .nh-assignment-meta {
        white-space: normal;
    }

    .nh-assignments .nh-assignment-empty {
        padding: 48px 18px;
    }

}
</style>

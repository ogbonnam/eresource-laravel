{{-- ========================================================= --}}
{{-- COURSE ACTIVITY --}}
{{-- ========================================================= --}}

<div class="nh-activity">

    <div class="nh-activity-card">

        {{-- ================================================= --}}
        {{-- HEADER --}}
        {{-- ================================================= --}}

        <div class="nh-activity-header">

            <div class="nh-activity-header-content">

                <h3 class="nh-activity-title">
                    Recent Course Activity
                </h3>

                <p class="nh-activity-description">
                    The latest activity recorded in this course.
                </p>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- ACTIVITY LIST --}}
        {{-- ================================================= --}}

        <div class="nh-activity-list">

            @forelse($activities as $activity)

                <div class="nh-activity-item">

                    {{-- ICON --}}
                    <div class="nh-activity-icon">

                        <x-dynamic-component
                            :component="$activity['icon']"
                            class="nh-activity-icon-svg"
                        />

                    </div>


                    {{-- CONTENT --}}
                    <div class="nh-activity-content">

                        <div class="nh-activity-item-title">
                            {{ $activity['title'] }}
                        </div>

                        <div class="nh-activity-item-description">
                            {{ $activity['description'] }}
                        </div>

                        <div class="nh-activity-date">
                            {{ $activity['date']->diffForHumans() }}
                        </div>

                    </div>

                </div>

            @empty

                <div class="nh-activity-empty">

                    <div class="nh-activity-empty-icon">
                        <x-heroicon-o-clock />
                    </div>

                    <div class="nh-activity-empty-title">
                        No activity recorded yet.
                    </div>

                    <div class="nh-activity-empty-description">
                        Course activity will appear here as students and teachers interact with the course.
                    </div>

                </div>

            @endforelse

        </div>

    </div>

</div>


<style>
/*
|--------------------------------------------------------------------------
| Noble Hall Course Activity
|--------------------------------------------------------------------------
| Completely isolated from Filament / Tailwind.
|
| All selectors begin with .nh-activity so Filament styles cannot
| accidentally override the component.
|--------------------------------------------------------------------------
*/


/* ============================================================
   ROOT
   ============================================================ */

.nh-activity {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    color: #111827;
    font-family: inherit;
}

.nh-activity *,
.nh-activity *::before,
.nh-activity *::after {
    box-sizing: border-box;
}


/* ============================================================
   CARD
   ============================================================ */

.nh-activity .nh-activity-card {
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


/* ============================================================
   HEADER
   ============================================================ */

.nh-activity .nh-activity-header {
    width: 100%;
    margin: 0;
    padding: 22px 24px;

    border-bottom: 1px solid #e5e7eb;

    background: #ffffff;
}

.nh-activity .nh-activity-header-content {
    width: 100%;
    margin: 0;
    padding: 0;
}

.nh-activity .nh-activity-title {
    margin: 0 !important;
    padding: 0 !important;

    font-size: 16px !important;
    line-height: 1.4 !important;
    font-weight: 700 !important;

    color: #111827 !important;
}

.nh-activity .nh-activity-description {
    margin: 5px 0 0 0 !important;
    padding: 0 !important;

    font-size: 13px !important;
    line-height: 1.5 !important;
    font-weight: 400 !important;

    color: #6b7280 !important;
}


/* ============================================================
   ACTIVITY LIST
   ============================================================ */

.nh-activity .nh-activity-list {
    width: 100%;
    margin: 0;
    padding: 0;
}


/* ============================================================
   ACTIVITY ITEM
   ============================================================ */

.nh-activity .nh-activity-item {
    width: 100%;

    display: flex;
    align-items: flex-start;

    gap: 14px;

    margin: 0;
    padding: 18px 24px;

    border-bottom: 1px solid #f3f4f6;

    background: #ffffff;

    transition:
        background-color 0.15s ease,
        border-color 0.15s ease;
}

.nh-activity .nh-activity-item:last-child {
    border-bottom: 0;
}

.nh-activity .nh-activity-item:hover {
    background: #fafafa;
}


/* ============================================================
   ICON
   ============================================================ */

.nh-activity .nh-activity-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 40px;

    width: 40px;
    height: 40px;

    margin: 0;
    padding: 0;

    border-radius: 12px;

    background: #eef2ff;
    color: #4f46e5;
}

.nh-activity .nh-activity-icon-svg {
    width: 20px !important;
    height: 20px !important;

    margin: 0 !important;
    padding: 0 !important;

    color: #4f46e5 !important;
}


/* ============================================================
   CONTENT
   ============================================================ */

.nh-activity .nh-activity-content {
    flex: 1 1 auto;

    min-width: 0;

    margin: 0;
    padding: 0;
}


/* ============================================================
   ITEM TITLE
   ============================================================ */

.nh-activity .nh-activity-item-title {
    margin: 0 !important;
    padding: 0 !important;

    font-size: 14px !important;
    line-height: 1.45 !important;
    font-weight: 600 !important;

    color: #111827 !important;
}


/* ============================================================
   ITEM DESCRIPTION
   ============================================================ */

.nh-activity .nh-activity-item-description {
    margin: 4px 0 0 0 !important;
    padding: 0 !important;

    font-size: 13px !important;
    line-height: 1.55 !important;
    font-weight: 400 !important;

    color: #6b7280 !important;
}


/* ============================================================
   DATE
   ============================================================ */

.nh-activity .nh-activity-date {
    margin: 5px 0 0 0 !important;
    padding: 0 !important;

    font-size: 11px !important;
    line-height: 1.4 !important;
    font-weight: 400 !important;

    color: #9ca3af !important;
}


/* ============================================================
   EMPTY STATE
   ============================================================ */

.nh-activity .nh-activity-empty {
    width: 100%;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    margin: 0;
    padding: 56px 24px;

    text-align: center;

    background: #ffffff;
}

.nh-activity .nh-activity-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 46px;
    height: 46px;

    margin: 0 0 12px 0;

    border-radius: 50%;

    background: #f3f4f6;
    color: #9ca3af;
}

.nh-activity .nh-activity-empty-icon svg {
    width: 22px !important;
    height: 22px !important;
}

.nh-activity .nh-activity-empty-title {
    margin: 0 !important;
    padding: 0 !important;

    font-size: 14px !important;
    line-height: 1.4 !important;
    font-weight: 600 !important;

    color: #374151 !important;
}

.nh-activity .nh-activity-empty-description {
    max-width: 460px;

    margin: 6px 0 0 0 !important;
    padding: 0 !important;

    font-size: 12px !important;
    line-height: 1.5 !important;
    font-weight: 400 !important;

    color: #9ca3af !important;
}


/* ============================================================
   DARK MODE
   ============================================================ */

.dark .nh-activity {
    color: #f9fafb;
}

.dark .nh-activity .nh-activity-card {
    border-color: #374151;
    background: #111827;
}

.dark .nh-activity .nh-activity-header {
    border-color: #374151;
    background: #111827;
}

.dark .nh-activity .nh-activity-title {
    color: #f9fafb !important;
}

.dark .nh-activity .nh-activity-description {
    color: #9ca3af !important;
}

.dark .nh-activity .nh-activity-item {
    border-color: #374151;
    background: #111827;
}

.dark .nh-activity .nh-activity-item:hover {
    background: #1f2937;
}

.dark .nh-activity .nh-activity-icon {
    background: #1e1b4b;
    color: #a5b4fc;
}

.dark .nh-activity .nh-activity-icon-svg {
    color: #a5b4fc !important;
}

.dark .nh-activity .nh-activity-item-title {
    color: #f9fafb !important;
}

.dark .nh-activity .nh-activity-item-description {
    color: #9ca3af !important;
}

.dark .nh-activity .nh-activity-date {
    color: #6b7280 !important;
}

.dark .nh-activity .nh-activity-empty {
    background: #111827;
}

.dark .nh-activity .nh-activity-empty-icon {
    background: #1f2937;
    color: #6b7280;
}

.dark .nh-activity .nh-activity-empty-title {
    color: #d1d5db !important;
}

.dark .nh-activity .nh-activity-empty-description {
    color: #6b7280 !important;
}


/* ============================================================
   MOBILE
   ============================================================ */

@media (max-width: 650px) {

    .nh-activity .nh-activity-header {
        padding: 18px;
    }

    .nh-activity .nh-activity-item {
        gap: 12px;
        padding: 16px 18px;
    }

    .nh-activity .nh-activity-icon {
        flex: 0 0 36px;

        width: 36px;
        height: 36px;

        border-radius: 10px;
    }

    .nh-activity .nh-activity-icon-svg {
        width: 18px !important;
        height: 18px !important;
    }

    .nh-activity .nh-activity-empty {
        padding: 45px 18px;
    }
}


/* ============================================================
   VERY SMALL MOBILE
   ============================================================ */

@media (max-width: 420px) {

    .nh-activity .nh-activity-header {
        padding: 16px;
    }

    .nh-activity .nh-activity-item {
        padding: 14px 16px;
    }

    .nh-activity .nh-activity-item-title {
        font-size: 13px !important;
    }

    .nh-activity .nh-activity-item-description {
        font-size: 12px !important;
    }
}
</style>
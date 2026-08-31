{{-- ========================================================= --}}
{{-- RESOURCES TAB --}}
{{-- ========================================================= --}}

<div class="nh-course-resources">

    {{-- ========================================================= --}}
    {{-- RESOURCE SUMMARY --}}
    {{-- ========================================================= --}}

    <div class="nh-resource-summary">

        {{-- TOTAL --}}
        <div class="nh-resource-stat">
            <div class="nh-resource-stat-content">
                <div>
                    <div class="nh-resource-stat-label">
                        Total Resources
                    </div>

                    <div class="nh-resource-stat-value">
                        {{ $resources->count() }}
                    </div>

                    <div class="nh-resource-stat-description">
                        All course resources
                    </div>
                </div>

                <div class="nh-resource-icon nh-resource-icon-primary">
                    <x-heroicon-o-folder />
                </div>
            </div>
        </div>


        {{-- PUBLISHED --}}
        <div class="nh-resource-stat">
            <div class="nh-resource-stat-content">
                <div>
                    <div class="nh-resource-stat-label">
                        Published
                    </div>

                    <div class="nh-resource-stat-value">
                        {{ $publishedResources->count() }}
                    </div>

                    <div class="nh-resource-stat-description">
                        Visible to students
                    </div>
                </div>

                <div class="nh-resource-icon nh-resource-icon-success">
                    <x-heroicon-o-check-circle />
                </div>
            </div>
        </div>


        {{-- UNPUBLISHED --}}
        <div class="nh-resource-stat">
            <div class="nh-resource-stat-content">
                <div>
                    <div class="nh-resource-stat-label">
                        Unpublished
                    </div>

                    <div class="nh-resource-stat-value">
                        {{ $unpublishedResources->count() }}
                    </div>

                    <div class="nh-resource-stat-description">
                        Draft or hidden resources
                    </div>
                </div>

                <div class="nh-resource-icon nh-resource-icon-warning">
                    <x-heroicon-o-eye-slash />
                </div>
            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- RESOURCE STATISTICS + TYPES --}}
    {{-- ========================================================= --}}

    <div class="nh-resource-overview">

        {{-- RESOURCE STATISTICS --}}
        <div class="nh-resource-panel">

            <div class="nh-resource-panel-header">
                <h3>Resource Statistics</h3>

                <p>
                    Overview of the resources available in this course.
                </p>
            </div>

            <div class="nh-resource-stat-list">

                <div class="nh-resource-stat-row">
                    <div class="nh-resource-stat-row-info">
                        <span class="nh-resource-stat-row-label">
                            Total
                        </span>

                        <span class="nh-resource-stat-row-description">
                            All resources
                        </span>
                    </div>

                    <strong>
                        {{ $resources->count() }}
                    </strong>
                </div>


                <div class="nh-resource-stat-row">
                    <div class="nh-resource-stat-row-info">
                        <span class="nh-resource-stat-row-label">
                            Published
                        </span>

                        <span class="nh-resource-stat-row-description">
                            Available to students
                        </span>
                    </div>

                    <strong class="nh-resource-value-success">
                        {{ $publishedResources->count() }}
                    </strong>
                </div>


                <div class="nh-resource-stat-row">
                    <div class="nh-resource-stat-row-info">
                        <span class="nh-resource-stat-row-label">
                            Unpublished
                        </span>

                        <span class="nh-resource-stat-row-description">
                            Not visible to students
                        </span>
                    </div>

                    <strong class="nh-resource-value-warning">
                        {{ $unpublishedResources->count() }}
                    </strong>
                </div>

            </div>

        </div>


        {{-- RESOURCE TYPES --}}
        <div class="nh-resource-panel nh-resource-types-panel">

            <div class="nh-resource-panel-header">
                <h3>Resource Types</h3>

                <p>
                    Breakdown of resources by file or content type.
                </p>
            </div>


            <div class="nh-resource-types">

                @forelse($resourceTypes as $type => $count)

                    <div class="nh-resource-type-card">

                        <div class="nh-resource-type-icon">
                            @php
                                $resourceType = strtolower((string) $type);
                            @endphp

                            @if(str_contains($resourceType, 'pdf'))
                                <x-heroicon-o-document-text />
                            @elseif(
                                str_contains($resourceType, 'doc') ||
                                str_contains($resourceType, 'word')
                            )
                                <x-heroicon-o-document />
                            @elseif(
                                str_contains($resourceType, 'video') ||
                                str_contains($resourceType, 'mp4')
                            )
                                <x-heroicon-o-video-camera />
                            @elseif(
                                str_contains($resourceType, 'zip') ||
                                str_contains($resourceType, 'archive')
                            )
                                <x-heroicon-o-archive-box />
                            @else
                                <x-heroicon-o-document />
                            @endif
                        </div>

                        <div class="nh-resource-type-content">

                            <div class="nh-resource-type-count">
                                {{ $count }}
                            </div>

                            <div class="nh-resource-type-label">
                                {{ $type ?: 'Other' }}
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="nh-resource-empty nh-resource-empty-types">
                        <x-heroicon-o-folder-open />

                        <strong>
                            No resources yet
                        </strong>

                        <span>
                            Resources added to this course will appear here.
                        </span>
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- RECENT RESOURCES --}}
    {{-- ========================================================= --}}

    <div class="nh-resource-panel nh-recent-resources">

        <div class="nh-resource-panel-header">
            <h3>Recent Resources</h3>

            <p>
                The latest resources added or updated in this course.
            </p>
        </div>


        <div class="nh-resource-list">

            @forelse($resources->take(20) as $resource)

                <div class="nh-resource-row">

                    {{-- ICON --}}
                    <div class="nh-resource-file-icon">

                        @php
                            $resourceType = strtolower((string) $resource->type);
                        @endphp

                        @if(str_contains($resourceType, 'pdf'))
                            <x-heroicon-o-document-text />
                        @elseif(
                            str_contains($resourceType, 'doc') ||
                            str_contains($resourceType, 'word')
                        )
                            <x-heroicon-o-document />
                        @elseif(
                            str_contains($resourceType, 'video') ||
                            str_contains($resourceType, 'mp4')
                        )
                            <x-heroicon-o-video-camera />
                        @elseif(
                            str_contains($resourceType, 'zip') ||
                            str_contains($resourceType, 'archive')
                        )
                            <x-heroicon-o-archive-box />
                        @else
                            <x-heroicon-o-document />
                        @endif

                    </div>


                    {{-- RESOURCE INFORMATION --}}
                    <div class="nh-resource-information">

                        <div class="nh-resource-title">
                            {{ $resource->title }}
                        </div>

                        <div class="nh-resource-meta">

                            <span>
                                {{ $resource->type ?: 'Resource' }}
                            </span>

                            <span class="nh-resource-meta-separator">
                                ·
                            </span>

                            <span>
                                Updated
                                {{ $resource->updated_at?->diffForHumans() }}
                            </span>

                        </div>

                    </div>


                    {{-- STATUS --}}
                    <div class="nh-resource-status">

                        @if($resource->is_published)

                            <span class="nh-resource-badge nh-resource-badge-success">
                                <x-heroicon-o-check-circle />

                                Published
                            </span>

                        @else

                            <span class="nh-resource-badge nh-resource-badge-neutral">
                                <x-heroicon-o-pencil />

                                Draft
                            </span>

                        @endif

                    </div>

                </div>

            @empty

                <div class="nh-resource-empty">

                    <x-heroicon-o-folder-open />

                    <strong>
                        No resources found
                    </strong>

                    <span>
                        Resources added to this course will appear here.
                    </span>

                </div>

            @endforelse

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- RESOURCE STYLES --}}
{{-- ========================================================= --}}

<style>

    /*
    |--------------------------------------------------------------------------
    | Noble Hall Course Resources
    |--------------------------------------------------------------------------
    | Completely isolated from Filament / Tailwind.
    |--------------------------------------------------------------------------
    */

    .nh-course-resources,
    .nh-course-resources *,
    .nh-course-resources *::before,
    .nh-course-resources *::after {
        box-sizing: border-box;
    }


    .nh-course-resources {
        width: 100%;
        margin: 0;
        padding: 0;
        color: #111827;
        font-family: inherit;
    }


    /* =========================================================
       SUMMARY
       ========================================================= */

    .nh-course-resources .nh-resource-summary {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin: 0 0 24px 0;
    }


    .nh-course-resources .nh-resource-stat {
        flex: 1 1 240px;
        min-width: 220px;
        min-height: 150px;
        padding: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.04),
            0 4px 12px rgba(0, 0, 0, 0.04);
    }


    .nh-course-resources .nh-resource-stat-content {
        width: 100%;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }


    .nh-course-resources .nh-resource-stat-label {
        margin: 0;
        font-size: 13px;
        line-height: 1.4;
        font-weight: 600;
        color: #6b7280;
    }


    .nh-course-resources .nh-resource-stat-value {
        margin: 8px 0 0 0;
        font-size: 32px;
        line-height: 1.1;
        font-weight: 700;
        color: #111827;
    }


    .nh-course-resources .nh-resource-stat-description {
        margin: 8px 0 0 0;
        font-size: 12px;
        line-height: 1.5;
        color: #6b7280;
    }


    /* =========================================================
       ICONS
       ========================================================= */

    .nh-course-resources .nh-resource-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 48px;
        width: 48px;
        height: 48px;
        border-radius: 12px;
    }


    .nh-course-resources .nh-resource-icon svg {
        width: 24px;
        height: 24px;
    }


    .nh-course-resources .nh-resource-icon-primary {
        color: #4f46e5;
        background: #eef2ff;
    }


    .nh-course-resources .nh-resource-icon-success {
        color: #16a34a;
        background: #f0fdf4;
    }


    .nh-course-resources .nh-resource-icon-warning {
        color: #d97706;
        background: #fffbeb;
    }


    /* =========================================================
       OVERVIEW
       ========================================================= */

    .nh-course-resources .nh-resource-overview {
        width: 100%;
        display: flex;
        align-items: stretch;
        gap: 24px;
        margin: 0 0 24px 0;
    }


    .nh-course-resources .nh-resource-panel {
        width: 100%;
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.04),
            0 4px 12px rgba(0, 0, 0, 0.04);
    }


    .nh-course-resources .nh-resource-overview > .nh-resource-panel:first-child {
        flex: 1 1 35%;
    }


    .nh-course-resources .nh-resource-types-panel {
        flex: 1 1 65%;
    }


    /* =========================================================
       PANEL HEADER
       ========================================================= */

    .nh-course-resources .nh-resource-panel-header {
        width: 100%;
        padding: 22px 24px;
        border-bottom: 1px solid #e5e7eb;
    }


    .nh-course-resources .nh-resource-panel-header h3 {
        margin: 0;
        padding: 0;
        font-size: 16px;
        line-height: 1.4;
        font-weight: 700;
        color: #111827;
    }


    .nh-course-resources .nh-resource-panel-header p {
        margin: 5px 0 0 0;
        padding: 0;
        font-size: 13px;
        line-height: 1.5;
        color: #6b7280;
    }


    /* =========================================================
       STAT LIST
       ========================================================= */

    .nh-course-resources .nh-resource-stat-list {
        width: 100%;
        padding: 6px 24px;
    }


    .nh-course-resources .nh-resource-stat-row {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 17px 0;
        border-bottom: 1px solid #f3f4f6;
    }


    .nh-course-resources .nh-resource-stat-row:last-child {
        border-bottom: 0;
    }


    .nh-course-resources .nh-resource-stat-row-info {
        min-width: 0;
    }


    .nh-course-resources .nh-resource-stat-row-label {
        display: block;
        font-size: 14px;
        line-height: 1.4;
        font-weight: 600;
        color: #374151;
    }


    .nh-course-resources .nh-resource-stat-row-description {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        line-height: 1.4;
        color: #9ca3af;
    }


    .nh-course-resources .nh-resource-stat-row > strong {
        flex-shrink: 0;
        font-size: 20px;
        line-height: 1.2;
        color: #111827;
    }


    .nh-course-resources .nh-resource-value-success {
        color: #16a34a !important;
    }


    .nh-course-resources .nh-resource-value-warning {
        color: #d97706 !important;
    }


    /* =========================================================
       RESOURCE TYPES
       ========================================================= */

    .nh-course-resources .nh-resource-types {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        padding: 24px;
    }


    .nh-course-resources .nh-resource-type-card {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 16px;
        border: 1px solid #f3f4f6;
        border-radius: 12px;
        background: #f9fafb;
    }


    .nh-course-resources .nh-resource-type-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
    }


    .nh-course-resources .nh-resource-type-icon svg {
        width: 20px;
        height: 20px;
    }


    .nh-course-resources .nh-resource-type-content {
        min-width: 0;
    }


    .nh-course-resources .nh-resource-type-count {
        font-size: 22px;
        line-height: 1.2;
        font-weight: 700;
        color: #111827;
    }


    .nh-course-resources .nh-resource-type-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-top: 3px;
        font-size: 11px;
        line-height: 1.4;
        text-transform: capitalize;
        color: #6b7280;
    }


    /* =========================================================
       RECENT RESOURCES
       ========================================================= */

    .nh-course-resources .nh-recent-resources {
        margin: 0 0 24px 0;
    }


    .nh-course-resources .nh-resource-list {
        width: 100%;
        margin: 0;
        padding: 0;
    }


    .nh-course-resources .nh-resource-row {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 17px 24px;
        border-bottom: 1px solid #f3f4f6;
    }


    .nh-course-resources .nh-resource-row:last-child {
        border-bottom: 0;
    }


    /* =========================================================
       FILE ICON
       ========================================================= */

    .nh-course-resources .nh-resource-file-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
    }


    .nh-course-resources .nh-resource-file-icon svg {
        width: 21px;
        height: 21px;
    }


    /* =========================================================
       RESOURCE INFORMATION
       ========================================================= */

    .nh-course-resources .nh-resource-information {
        flex: 1 1 auto;
        min-width: 0;
    }


    .nh-course-resources .nh-resource-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0;
        font-size: 14px;
        line-height: 1.4;
        font-weight: 600;
        color: #111827;
    }


    .nh-course-resources .nh-resource-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 4px;
        font-size: 11px;
        line-height: 1.4;
        color: #6b7280;
    }


    .nh-course-resources .nh-resource-meta-separator {
        color: #d1d5db;
    }


    /* =========================================================
       STATUS
       ========================================================= */

    .nh-course-resources .nh-resource-status {
        flex: 0 0 auto;
    }


    .nh-course-resources .nh-resource-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 600;
        white-space: nowrap;
    }


    .nh-course-resources .nh-resource-badge svg {
        width: 14px;
        height: 14px;
    }


    .nh-course-resources .nh-resource-badge-success {
        color: #15803d;
        background: #f0fdf4;
    }


    .nh-course-resources .nh-resource-badge-neutral {
        color: #4b5563;
        background: #f3f4f6;
    }


    /* =========================================================
       EMPTY STATES
       ========================================================= */

    .nh-course-resources .nh-resource-empty {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 45px 24px;
        text-align: center;
    }


    .nh-course-resources .nh-resource-empty svg {
        width: 36px;
        height: 36px;
        margin-bottom: 4px;
        color: #9ca3af;
    }


    .nh-course-resources .nh-resource-empty strong {
        margin: 0;
        font-size: 14px;
        line-height: 1.4;
        color: #374151;
    }


    .nh-course-resources .nh-resource-empty span {
        font-size: 12px;
        line-height: 1.4;
        color: #9ca3af;
    }


    /* =========================================================
       DARK MODE
       ========================================================= */

    .dark .nh-course-resources {
        color: #f9fafb;
    }


    .dark .nh-course-resources .nh-resource-stat,
    .dark .nh-course-resources .nh-resource-panel {
        border-color: #374151;
        background: #111827;
    }


    .dark .nh-course-resources .nh-resource-stat-value,
    .dark .nh-course-resources .nh-resource-panel-header h3,
    .dark .nh-course-resources .nh-resource-stat-row > strong,
    .dark .nh-course-resources .nh-resource-type-count,
    .dark .nh-course-resources .nh-resource-title {
        color: #f9fafb;
    }


    .dark .nh-course-resources .nh-resource-stat-label,
    .dark .nh-course-resources .nh-resource-stat-description,
    .dark .nh-course-resources .nh-resource-panel-header p,
    .dark .nh-course-resources .nh-resource-stat-row-description,
    .dark .nh-course-resources .nh-resource-meta,
    .dark .nh-course-resources .nh-resource-type-label {
        color: #9ca3af;
    }


    .dark .nh-course-resources .nh-resource-panel-header,
    .dark .nh-course-resources .nh-resource-stat-row,
    .dark .nh-course-resources .nh-resource-row {
        border-color: #374151;
    }


    .dark .nh-course-resources .nh-resource-type-card {
        border-color: #374151;
        background: #1f2937;
    }


    .dark .nh-course-resources .nh-resource-type-icon,
    .dark .nh-course-resources .nh-resource-file-icon {
        background: #1e1b4b;
        color: #a5b4fc;
    }


    .dark .nh-course-resources .nh-resource-badge-neutral {
        background: #1f2937;
        color: #d1d5db;
    }


    .dark .nh-course-resources .nh-resource-empty strong {
        color: #f3f4f6;
    }


    .dark .nh-course-resources .nh-resource-empty span {
        color: #9ca3af;
    }


    /* =========================================================
       TABLET
       ========================================================= */

    @media (max-width: 1000px) {

        .nh-course-resources .nh-resource-overview {
            flex-direction: column;
        }

        .nh-course-resources .nh-resource-overview > .nh-resource-panel:first-child,
        .nh-course-resources .nh-resource-types-panel {
            width: 100%;
            flex: none;
        }

        .nh-course-resources .nh-resource-types {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }


    /* =========================================================
       SMALL TABLET
       ========================================================= */

    @media (max-width: 750px) {

        .nh-course-resources .nh-resource-summary {
            flex-direction: column;
            gap: 14px;
        }

        .nh-course-resources .nh-resource-stat {
            width: 100%;
            min-width: 0;
        }

        .nh-course-resources .nh-resource-types {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 600px) {

        .nh-course-resources .nh-resource-stat {
            padding: 18px;
        }

        .nh-course-resources .nh-resource-panel-header {
            padding: 18px;
        }

        .nh-course-resources .nh-resource-types {
            grid-template-columns: 1fr;
            padding: 18px;
        }

        .nh-course-resources .nh-resource-row {
            align-items: flex-start;
            padding: 15px 18px;
        }

        .nh-course-resources .nh-resource-status {
            flex-shrink: 0;
        }

        .nh-course-resources .nh-resource-badge {
            font-size: 10px;
            padding: 5px 8px;
        }
    }


    /* =========================================================
       VERY SMALL MOBILE
       ========================================================= */

    @media (max-width: 430px) {

        .nh-course-resources .nh-resource-row {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 12px;
        }

        .nh-course-resources .nh-resource-status {
            grid-column: 2;
        }

        .nh-course-resources .nh-resource-stat-value {
            font-size: 28px;
        }
    }

</style>

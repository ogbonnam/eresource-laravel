<x-filament-panels::page>
    <style>
        .lp-dept-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .lp-dept-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            padding: 18px;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .lp-dept-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
            border-color: #f59e0b;
            transform: translateY(-2px);
        }

        .lp-dept-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .lp-dept-name {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .lp-dept-badge {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 24px;
            padding: 0 8px;
            border-radius: 999px;
            background: #fef3c7;
            color: #92400e;
            font-size: 12px;
            font-weight: 700;
        }

        .lp-dept-sub {
            margin: 10px 0 0 0;
            font-size: 13px;
            color: #6b7280;
        }

        .lp-dept-empty {
            font-size: 14px;
            color: #6b7280;
        }

        html.dark .lp-dept-card {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.1);
        }

        html.dark .lp-dept-name {
            color: #f9fafb;
        }

        html.dark .lp-dept-sub {
            color: #9ca3af;
        }

        html.dark .lp-dept-badge {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }
    </style>

    <div class="lp-dept-grid">
        @forelse ($faculties as $faculty)
            <a href="{{ $this->getFacultyUrl($faculty) }}" class="lp-dept-card">
                <div class="lp-dept-card-header">
                    <p class="lp-dept-name">{{ $faculty->name }}</p>
                    <span class="lp-dept-badge">{{ $faculty->lesson_plans_count }}</span>
                </div>
                <p class="lp-dept-sub">
                    Approved lesson plan{{ $faculty->lesson_plans_count === 1 ? '' : 's' }}
                </p>
            </a>
        @empty
            <p class="lp-dept-empty">No departments found.</p>
        @endforelse
    </div>
</x-filament-panels::page>
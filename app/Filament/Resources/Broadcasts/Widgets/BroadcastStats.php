<?php

namespace App\Filament\Resources\Broadcasts\Widgets;

use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BroadcastStats extends StatsOverviewWidget
{
    public ?Broadcast $record = null;

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $recipients = BroadcastRecipient::query()
            ->where('broadcast_id', $this->record->id);

        $totalRecipients = (clone $recipients)->count();

        $viewed = (clone $recipients)
            ->whereNotNull('read_at')
            ->count();

        $notViewed = $totalRecipients - $viewed;

        $totalViews = (int) (clone $recipients)
            ->sum('open_count');

        $acknowledged = (clone $recipients)
            ->whereNotNull('acknowledged_at')
            ->count();

        $notAcknowledged = $totalRecipients - $acknowledged;

        $readRate = $totalRecipients > 0
            ? round(($viewed / $totalRecipients) * 100)
            : 0;

        return [
            Stat::make(
                'Recipients',
                number_format($totalRecipients)
            )
                ->description('Teachers who received this broadcast')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(
                'Viewed',
                number_format($viewed)
            )
                ->description("{$readRate}% read rate")
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make(
                'Not Viewed',
                number_format($notViewed)
            )
                ->description('Teachers who have not opened it')
                ->descriptionIcon('heroicon-m-eye-slash')
                ->color('warning'),

            Stat::make(
                'Total Views',
                number_format($totalViews)
            )
                ->description('Total broadcast openings')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make(
                'Acknowledged',
                number_format($acknowledged)
            )
                ->description('Teachers who acknowledged')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(
                'Not Acknowledged',
                number_format($notAcknowledged)
            )
                ->description('Awaiting acknowledgement')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make(
                'Read Rate',
                "{$readRate}%"
            )
                ->description(
                    "{$viewed} of {$totalRecipients} viewed"
                )
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color(
                    $readRate >= 80
                        ? 'success'
                        : (
                            $readRate >= 50
                                ? 'warning'
                                : 'danger'
                        )
                ),
        ];
    }
}
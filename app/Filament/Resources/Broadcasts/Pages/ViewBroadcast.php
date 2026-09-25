<?php

namespace App\Filament\Resources\Broadcasts\Pages;

use App\Filament\Resources\Broadcasts\BroadcastResource;
use App\Filament\Resources\Broadcasts\Widgets\BroadcastStats;
use Filament\Resources\Pages\ViewRecord;

class ViewBroadcast extends ViewRecord
{
    protected static string $resource = BroadcastResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BroadcastStats::class,
        ];
    }
}
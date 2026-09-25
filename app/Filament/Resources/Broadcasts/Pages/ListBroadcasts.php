<?php

namespace App\Filament\Resources\Broadcasts\Pages;

use App\Filament\Resources\Broadcasts\BroadcastResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListBroadcasts extends ListRecords
{
    protected static string $resource = BroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Broadcast'),
        ];
    }
}
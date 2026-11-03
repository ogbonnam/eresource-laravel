<?php

namespace App\Filament\Resources\YearGroups\Pages;

use App\Filament\Resources\YearGroups\YearGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditYearGroup extends EditRecord
{
    protected static string $resource = YearGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

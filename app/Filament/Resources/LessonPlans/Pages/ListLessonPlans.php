<?php

namespace App\Filament\Resources\LessonPlans\Pages;

use App\Filament\Resources\LessonPlans\LessonPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListLessonPlans extends ListRecords
{
    protected static string $resource = LessonPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
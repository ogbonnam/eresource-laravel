<?php

namespace App\Filament\Pages;

use App\Filament\Resources\LessonPlans\LessonPlanResource;
use App\Models\Faculty;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class LessonPlanDepartments extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'School Management';

    protected static ?string $navigationLabel = 'Lesson Plans';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.lesson-plan-departments';

    public Collection $faculties;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function mount(): void
    {
        $this->faculties = Faculty::query()
            ->withCount(['lessonPlans as lesson_plans_count' => fn ($q) => $q->where('status', 'approved')])
            ->orderBy('name')
            ->get();
    }

    public function getFacultyUrl(Faculty $faculty): string
    {
        return LessonPlanResource::getUrl('index', [
            'tableFilters' => [
                'faculty_id' => ['value' => $faculty->id],
            ],
        ]);
    }
}
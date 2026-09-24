<?php

namespace App\Filament\Resources\LessonPlans;

use App\Filament\Resources\LessonPlans\Pages\ListLessonPlans;
use App\Filament\Resources\LessonPlans\Pages\ViewLessonPlan;
use App\Filament\Resources\LessonPlans\Schemas\LessonPlanInfolist;
use App\Filament\Resources\LessonPlans\Tables\LessonPlansTable;
use App\Models\LessonPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LessonPlanResource extends Resource
{
    protected static ?string $model = LessonPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'School Management';

    protected static ?string $navigationLabel = 'Lesson Plans';

    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * Admin only ever sees lesson plans that have been approved
     * (by HOD/HOF or otherwise vetted to approved status).
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'approved')
            ->with(['teacher', 'faculty', 'schoolClass', 'subject', 'vetter']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LessonPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonPlansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLessonPlans::route('/'),
            'view' => ViewLessonPlan::route('/{record}'),
        ];
    }
}
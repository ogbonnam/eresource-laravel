<?php

namespace App\Filament\Resources\LessonPlans\Tables;

use App\Models\Faculty;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LessonPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $facultyId = request()->input('tableFilters.faculty_id.value');

                if (filled($facultyId)) {
                    $query->where('lesson_plans.faculty_id', $facultyId);
                }

                return $query;
            })

            ->columns([
                TextColumn::make('faculty.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('topic')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('week')
                    ->label('Week')
                    ->sortable(),

                TextColumn::make('lesson_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('vetter.name')
                    ->label('Approved By')
                    ->sortable(),

                TextColumn::make('vetted_at')
                    ->label('Approved On')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('faculty_id')
                    ->label('Department')
                    ->options(
                        fn () => Faculty::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    ),

                SelectFilter::make('week')
                    ->label('Week')
                    ->options(
                        fn () => \App\Models\LessonPlan::query()
                            ->whereNotNull('week')
                            ->distinct()
                            ->orderBy('week')
                            ->pluck('week', 'week')
                            ->toArray()
                    ),
            ])

            ->recordActions([
                ViewAction::make(),
            ])

            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->defaultSort('lesson_date', 'desc');
    }
}
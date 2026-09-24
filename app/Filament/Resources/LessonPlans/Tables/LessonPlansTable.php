<?php

namespace App\Filament\Resources\LessonPlans\Tables;

use App\Models\Faculty;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LessonPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->options(fn () => Faculty::query()->pluck('name', 'id')),

                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->defaultSort('lesson_date', 'desc');
    }
}
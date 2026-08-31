<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAssignments extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Recent Assignments';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Assignment::query()
                    ->with([
                        'creator',
                        'course.schoolClass',
                        'course.subject',
                    ])
                    ->latest('created_at')
            )
            ->defaultPaginationPageOption(5)
            ->paginated([5])
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Assignment
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('title')
                    ->label('Assignment')
                    ->limit(28)
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unknown'),

                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make(
                    'course.schoolClass.name'
                )
                    ->label('Class')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No class'),

                /*
                |--------------------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make(
                    'course.subject.name'
                )
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No subject'),

                /*
                |--------------------------------------------------------------------------
                | Course
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->limit(22)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No course'),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('is_published')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string => $state
                            ? 'Published'
                            : 'Draft'
                    )
                    ->color(
                        fn (bool $state): string => $state
                            ? 'success'
                            : 'gray'
                    ),

                /*
                |--------------------------------------------------------------------------
                | Due Date
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('No deadline'),
            ]);
    }
}
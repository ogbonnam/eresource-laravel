<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentEnrollments extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 2;

    protected static ?string $heading = 'Recent Enrolments';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Enrollment::query()
                    ->with([
                        'user',
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
                | Student
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                /*
                |--------------------------------------------------------------------------
                | Email
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->limit(30),

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
                    ->limit(30)
                    ->searchable(),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => ucfirst($state)
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'active' => 'success',
                            'pending' => 'warning',
                            'completed' => 'info',
                            'dropped' => 'danger',
                            default => 'gray',
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | Date
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled')
                    ->dateTime('M d, Y')
                    ->since(),

            ]);
    }
}
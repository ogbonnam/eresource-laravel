<?php

namespace App\Filament\Widgets;

use App\Models\AssignmentSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSubmissions extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 2;

    protected static ?string $heading = 'Recent Submissions';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AssignmentSubmission::query()
                    ->with([
                        'student',
                        'assignment.creator',
                        'assignment.course.schoolClass',
                        'assignment.course.subject',
                    ])
                    ->latest('updated_at')
            )
            ->defaultPaginationPageOption(5)
            ->paginated([5])
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Student
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make(
                    'assignment.course.schoolClass.name'
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
                    'assignment.course.subject.name'
                )
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No subject'),

                /*
                |--------------------------------------------------------------------------
                | Assignment
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('assignment.title')
                    ->label('Assignment')
                    ->limit(30)
                    ->searchable()
                    ->weight('medium')
                    ->placeholder('Unknown assignment'),

                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make(
                    'assignment.creator.name'
                )
                    ->label('Teacher')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unknown'),

                /*
                |--------------------------------------------------------------------------
                | Course
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('assignment.course.name')
                    ->label('Course')
                    ->limit(25)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No course'),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => ucfirst($state)
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'graded' => 'success',
                            'submitted' => 'warning',
                            'draft' => 'gray',
                            default => 'gray',
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | Grade
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->placeholder('Not graded'),

                /*
                |--------------------------------------------------------------------------
                | Updated
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->since(),
            ]);
    }
}
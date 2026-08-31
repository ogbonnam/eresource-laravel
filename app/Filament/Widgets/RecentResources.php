<?php

namespace App\Filament\Widgets;

use App\Models\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentResources extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Recently Published Resources';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Resource::query()
                    ->with([
                        'course.teacher',
                        'course.schoolClass',
                        'course.subject',
                    ])
                    ->where('is_published', true)
                    ->latest('published_at')
            )
            ->defaultPaginationPageOption(5)
            ->paginated([5])
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Resource
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('title')
                    ->label('Resource')
                    ->limit(28)
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('course.teacher.name')
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
                | Resource Type
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => $state
                            ? strtoupper($state)
                            : 'FILE'
                    ),

                /*
                |--------------------------------------------------------------------------
                | Published
                |--------------------------------------------------------------------------
                */
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->since()
                    ->sortable(),
            ]);
    }
}
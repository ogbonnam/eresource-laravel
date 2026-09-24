<?php

namespace App\Filament\Resources\LessonPlans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class LessonPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->columns(3)
                    ->components([
                        TextEntry::make('faculty.name')->label('Department'),
                        TextEntry::make('teacher.name')->label('Teacher'),
                        TextEntry::make('subject.name')->label('Subject'),
                        TextEntry::make('schoolClass.name')->label('Class'),
                        TextEntry::make('week')->label('Week'),
                        TextEntry::make('lesson_date')->label('Date')->date('d M Y'),
                    ]),

                Section::make('Lesson Content')
                    ->columns(1)
                    ->components([
                        TextEntry::make('topic'),
                        TextEntry::make('objectives')->prose()->columnSpanFull(),
                        TextEntry::make('activities')->prose()->columnSpanFull(),
                        TextEntry::make('assessment')->prose()->columnSpanFull(),
                        TextEntry::make('file_path')
                            ->label('Uploaded Document')
                            ->formatStateUsing(fn (?string $state) => $state ? basename($state) : 'No document uploaded')
                            ->url(fn (?string $state) => $state ? Storage::url($state) : null)
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-paper-clip')
                            ->columnSpanFull(),
                    ]),

                Section::make('Approval')
                    ->columns(2)
                    ->components([
                        TextEntry::make('vetter.name')->label('Approved By'),
                        TextEntry::make('vetted_at')->label('Approved On')->dateTime('d M Y, h:i A'),
                        TextEntry::make('teacher_comment')->label('Teacher Comment')->columnSpanFull(),
                        TextEntry::make('vetter_comment')->label('Vetter Comment')->columnSpanFull(),
                    ]),
            ]);
    }
}
<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('past_paper_id')
                    ->relationship('pastPaper', 'title'),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                TextInput::make('class_id')
                    ->numeric(),
                TextInput::make('topic'),
                TextInput::make('subtopic'),
                TextInput::make('concept'),
                TextInput::make('question_type')
                    ->required()
                    ->default('multiple_choice'),
                TextInput::make('difficulty')
                    ->required()
                    ->default('medium'),
                TextInput::make('command_word'),
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('marks')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('answer')
                    ->columnSpanFull(),
                Textarea::make('explanation')
                    ->columnSpanFull(),
                Select::make('source_type')
                    ->options(['past_paper' => 'Past paper', 'ai_generated' => 'Ai generated', 'manual' => 'Manual'])
                    ->default('ai_generated')
                    ->required(),
                Select::make('source_question_id')
                    ->relationship('sourceQuestion', 'id'),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'pending_review' => 'Pending review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ])
                    ->default('draft')
                    ->required(),
                TextInput::make('reviewed_by')
                    ->numeric(),
                DateTimePicker::make('reviewed_at'),
                TextInput::make('ai_model'),
                Textarea::make('generation_notes')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}

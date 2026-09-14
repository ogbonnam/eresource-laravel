<?php

namespace App\Filament\Resources\PastPapers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PastPaperForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                TextInput::make('class_id')
                    ->numeric(),
                TextInput::make('uploaded_by')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('exam_type'),
                TextInput::make('exam_year'),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('file_name'),
                TextInput::make('mime_type'),
                Textarea::make('extracted_text')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
            'uploaded' => 'Uploaded',
            'processing' => 'Processing',
            'processed' => 'Processed',
            'failed' => 'Failed',
        ])
                    ->default('uploaded')
                    ->required(),
                Textarea::make('processing_error')
                    ->columnSpanFull(),
                DateTimePicker::make('processed_at'),
            ]);
    }
}

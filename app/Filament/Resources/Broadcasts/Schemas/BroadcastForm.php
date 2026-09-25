<?php

namespace App\Filament\Resources\Broadcasts\Schemas;

use App\Models\Faculty;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BroadcastForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Subject / Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('target_type')
                    ->label('Send To')
                    ->options([
                        'all' => 'All Teachers',
                        'department' => 'Teachers in a Department',
                        'individual' => 'Selected Teachers',
                    ])
                    ->required()
                    ->live()
                    ->default('all')
                    ->columnSpanFull(),

                Select::make('department_id')
                    ->label('Department')
                    ->options(
                        fn () => Faculty::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->required(fn ($get) => $get('target_type') === 'department')
                    ->visible(fn ($get) => $get('target_type') === 'department')
                    ->columnSpanFull(),

                Select::make('teacher_ids')
                    ->label('Teachers')
                    ->multiple()
                    ->options(
                        fn () => User::query()
                            ->where('role', 'teacher')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => $get('target_type') === 'individual')
                    ->visible(fn ($get) => $get('target_type') === 'individual')
                    ->columnSpanFull(),

                RichEditor::make('message')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->disk('local')
                    ->directory('broadcasts')
                    ->acceptedFileTypes([
                        'application/pdf',

                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',

                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(51200)
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),

                TextInput::make('google_sheet_url')
                    ->label('Google Sheet URL')
                    ->url()
                    ->placeholder('https://docs.google.com/spreadsheets/...')
                    ->helperText('Optional. Teachers will be able to open the sheet from the broadcast.')
                    ->columnSpanFull(),
            ]);
    }
}
<?php

namespace App\Filament\Resources\PastPapers;

use App\Filament\Resources\PastPapers\Pages\CreatePastPaper;
use App\Filament\Resources\PastPapers\Pages\EditPastPaper;
use App\Filament\Resources\PastPapers\Pages\ListPastPapers;
use App\Models\PastPaper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\MarkScheme;
use App\Services\PastPaperTextExtractor;
use App\Services\MarkSchemeAnalyzer;
use Filament\Notifications\Notification;


use App\Services\PastPaperQuestionAnalyzer;
use Filament\Actions\Action;

class PastPaperResource extends Resource
{
    protected static ?string $model = PastPaper::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Past Papers';

    protected static ?string $modelLabel = 'Past Paper';

    protected static ?string $pluralModelLabel = 'Past Papers';

    protected static string|\UnitEnum|null $navigationGroup = 'Question Bank';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('class_id')
                    ->label('Class / Level')
                    ->relationship('schoolClass', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('title')
                    ->label('Past Paper Title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Mathematics Checkpoint 2025'),

                TextInput::make('exam_type')
                    ->label('Exam Type')
                    ->maxLength(100)
                    ->placeholder('e.g. Checkpoint, IGCSE, Mock Examination'),

                TextInput::make('exam_year')
                    ->label('Exam Year')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(2100)
                    ->placeholder('e.g. 2025'),

                FileUpload::make('file_path')
                    ->label('Past Paper File')
                    ->disk('public')
                    ->directory('past-papers')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(20480)
                    ->required()
                    ->downloadable()
                    ->openable(),

                Textarea::make('extracted_text')
                    ->label('Extracted Text')
                    ->rows(10)
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (?PastPaper $record): bool => filled($record?->extracted_text)),

                TextInput::make('status')
                    ->label('Processing Status')
                    ->disabled()
                    ->dehydrated(false)
                    ->default('uploaded'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Past Paper')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class / Level')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam_type')
                    ->label('Exam Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('exam_year')
                    ->label('Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'uploaded' => 'gray',
                        'processing' => 'warning',
                        'processed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('markSchemes.status')
                    ->label('Mark Scheme')
                    ->badge()
                    ->state(function (PastPaper $record): string {
                        $markScheme = $record->markSchemes()
                            ->latest()
                            ->first();

                        return $markScheme?->status ?? 'Not uploaded';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'processed' => 'success',
                        'analyzed' => 'success',
                        'processing' => 'warning',
                        'analyzing' => 'warning',
                        'failed' => 'danger',
                        'uploaded' => 'gray',
                        'Not uploaded' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class / Level')
                    ->relationship('schoolClass', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'uploaded' => 'Uploaded',
                        'processing' => 'Processing',
                        'processed' => 'Processed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Action::make('uploadMarkScheme')
                    ->label('Upload Mark Scheme')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->modalHeading(fn (PastPaper $record): string =>
                        'Upload Mark Scheme · ' . $record->title
                    )
                    ->modalDescription(
                        'Upload the official mark scheme for this past paper. '
                        . 'The system will extract its text so it can later be analyzed '
                        . 'into structured marking rules.'
                    )
                    ->schema([
                        FileUpload::make('file')
                            ->label('Mark Scheme File')
                            ->disk('public')
                            ->directory('mark-schemes')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(20480)
                            ->required()
                            ->downloadable()
                            ->openable()
                            ->helperText(
                                'Accepted formats: PDF, DOC and DOCX. Maximum size: 20 MB.'
                            ),
                    ])
                    ->modalSubmitActionLabel('Upload & Extract')
                    ->action(function (
                        PastPaper $record,
                        array $data
                    ): void {
                        $filePath = $data['file'] ?? null;

                        if (blank($filePath)) {
                            Notification::make()
                                ->title('No mark scheme selected')
                                ->danger()
                                ->send();

                            return;
                        }

                        $markScheme = MarkScheme::create([
                            'past_paper_id' => $record->id,
                            'file_path' => $filePath,
                            'file_name' => basename($filePath),
                            'status' => 'uploaded',
                        ]);

                        try {
                            $markScheme->update([
                                'status' => 'processing',
                                'processing_error' => null,
                            ]);

                            $absolutePath = storage_path(
                                'app/public/' . ltrim($filePath, '/')
                            );

                            $mimeType = mime_content_type(
                                $absolutePath
                            );

                            if (blank($mimeType)) {
                                throw new \RuntimeException(
                                    'Unable to determine the MIME type of the mark scheme.'
                                );
                            }

                            $text = app(PastPaperTextExtractor::class)
                                ->extractFile($absolutePath);

                            if (blank($text)) {
                                throw new \RuntimeException(
                                    'No text could be extracted from the mark scheme.'
                                );
                            }

                            $markScheme->update([
                                'mime_type' => $mimeType,
                                'extracted_text' => $text,
                                'status' => 'processed',
                                'processing_error' => null,
                                'processed_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Mark scheme uploaded')
                                ->body(
                                    'The mark scheme was uploaded and its text was extracted successfully.'
                                )
                                ->success()
                                ->send();

                        } catch (\Throwable $e) {

                            $markScheme->update([
                                'status' => 'failed',
                                'processing_error' => $e->getMessage(),
                            ]);

                            report($e);

                            Notification::make()
                                ->title('Mark scheme extraction failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('analyzeMarkScheme')
                    ->label('Analyze Mark Scheme')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->visible(fn (PastPaper $record): bool =>
                        $record->markSchemes()
                            ->where('status', ['processed', 'analyzed'])
                            ->exists()
                    )
                    ->requiresConfirmation()
                    ->modalHeading(fn (PastPaper $record): string =>
                        'Analyze Mark Scheme · ' . $record->title
                    )
                    ->modalDescription(
                        'Gemini will analyze the extracted mark scheme and convert '
                        . 'the official marking instructions into structured marking '
                        . 'rules for the questions in this past paper.'
                    )
                    ->modalSubmitActionLabel('Analyze Mark Scheme')
                    ->action(function (PastPaper $record): void {

                        $markScheme = $record->markSchemes()
                            ->where('status', ['processed', 'analyzed'])
                            ->latest('id')
                            ->first();

                        if (! $markScheme) {
                            Notification::make()
                                ->title('No processed mark scheme found')
                                ->body(
                                    'Upload and process a mark scheme before analyzing it.'
                                )
                                ->danger()
                                ->send();

                            return;
                        }

                        if (blank($markScheme->extracted_text)) {
                            Notification::make()
                                ->title('Mark scheme has no extracted text')
                                ->body(
                                    'The mark scheme must contain extracted text before it can be analyzed.'
                                )
                                ->danger()
                                ->send();

                            return;
                        }

                        try {
                            $markScheme->update([
                                'status' => 'analyzing',
                                'processing_error' => null,
                            ]);

                            $analyzer = app(MarkSchemeAnalyzer::class);

                            $result = $analyzer->analyze(
                                $markScheme->extracted_text
                            );

                            $analyzer->attachToPastPaper(
                                $record,
                                $result
                            );

                            $markScheme->update([
                                'status' => 'analyzed',
                                'processing_error' => null,
                            ]);

                            $questionCount = count(
                                $result['questions'] ?? []
                            );

                            Notification::make()
                                ->title('Mark scheme analyzed successfully')
                                ->body(
                                    "{$questionCount} question marking rules were extracted and attached to the past paper."
                                )
                                ->success()
                                ->send();

                        } catch (\Throwable $e) {

                            $markScheme->update([
                                'status' => 'failed',
                                'processing_error' => $e->getMessage(),
                            ]);

                            report($e);

                            Notification::make()
                                ->title('Mark scheme analysis failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('analyzeQuestions')
                    ->label('Analyze Questions')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->visible(fn (PastPaper $record): bool =>
                        $record->status === 'processed'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Analyze Past Paper Questions')
                    ->modalDescription(
                        'The AI will analyze this past paper and identify its questions, topics, concepts, difficulty, marks, answers, and other question metadata.'
                    )
                    ->modalSubmitActionLabel('Analyze Questions')
                    ->action(function (PastPaper $record): void {
                        app(PastPaperQuestionAnalyzer::class)
                            ->analyze($record);
                    }),

                EditAction::make(),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPastPapers::route('/'),
            'create' => CreatePastPaper::route('/create'),
            'edit' => EditPastPaper::route('/{record}/edit'),
        ];
    }
}
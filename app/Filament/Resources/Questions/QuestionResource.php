<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Questions\Pages\ReviewQuestion;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'Questions';

    protected static ?string $modelLabel = 'Question';

    protected static ?string $pluralModelLabel = 'Questions';

    protected static string|\UnitEnum|null $navigationGroup =
        'Question Bank';

    protected static ?int $navigationSort = 2;

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | SOURCE
            |--------------------------------------------------------------------------
            */

            Select::make('past_paper_id')
                ->label('Source Past Paper')
                ->relationship('pastPaper', 'title')
                ->searchable()
                ->preload()
                ->nullable()
                ->columnSpanFull(),

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

            /*
            |--------------------------------------------------------------------------
            | CLASSIFICATION
            |--------------------------------------------------------------------------
            */

            TextInput::make('topic')
                ->label('Topic')
                ->maxLength(255),

            TextInput::make('subtopic')
                ->label('Subtopic')
                ->maxLength(255),

            TextInput::make('concept')
                ->label('Concept')
                ->maxLength(255),

            Select::make('question_type')
                ->label('Question Type')
                ->options([
                    'multiple_choice' => 'Multiple Choice',
                    'short_answer' => 'Short Answer',
                    'structured' => 'Structured',
                    'matching' => 'Matching',
                    'note_taking' => 'Note Taking',
                    'writing' => 'Writing',
                    'email' => 'Email Writing',
                    'essay' => 'Essay Writing',
                    'true_false' => 'True / False',
                    'fill_blank' => 'Fill in the Blank',
                    'other' => 'Other',
                ])
                ->required()
                ->searchable(),

            Select::make('difficulty')
                ->label('Difficulty')
                ->options([
                    'easy' => 'Easy',
                    'medium' => 'Medium',
                    'hard' => 'Hard',
                ])
                ->required(),

            TextInput::make('command_word')
                ->label('Command Word')
                ->placeholder('e.g. Explain, Identify, Write')
                ->maxLength(255),

            TextInput::make('marks')
                ->label('Marks')
                ->numeric()
                ->minValue(1)
                ->required(),

            /*
            |--------------------------------------------------------------------------
            | QUESTION
            |--------------------------------------------------------------------------
            */

            Textarea::make('question')
                ->label('Question')
                ->required()
                ->rows(10)
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | OPTIONS
            |--------------------------------------------------------------------------
            */

            Repeater::make('options')
                ->label('Answer Options')
                ->relationship()
                ->schema([

                    TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->maxLength(5)
                        ->placeholder('A'),

                    Textarea::make('option_text')
                        ->label('Option')
                        ->required()
                        ->rows(2),

                    Select::make('is_correct')
                        ->label('Correct Answer')
                        ->options([
                            1 => 'Correct',
                            0 => 'Incorrect',
                        ])
                        ->default(0)
                        ->required(),

                    TextInput::make('sort_order')
                        ->label('Order')
                        ->numeric()
                        ->default(0)
                        ->required(),

                ])
                ->columns(2)
                ->reorderable('sort_order')
                ->defaultItems(0)
                ->addActionLabel('Add Option')
                ->columnSpanFull()
                ->visible(fn ($get): bool =>
                    $get('question_type') === 'multiple_choice'
                ),

            /*
            |--------------------------------------------------------------------------
            | ANSWER
            |--------------------------------------------------------------------------
            */

            Textarea::make('answer')
                ->label('Answer')
                ->rows(7)
                ->helperText(
                    'Review and correct the AI-extracted answer before approving the question.'
                )
                ->columnSpanFull(),

            Textarea::make('explanation')
                ->label('Explanation')
                ->rows(8)
                ->helperText(
                    'Explain why the answer is correct. This explanation will later be shown to students during review.'
                )
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | REVIEW INFORMATION
            |--------------------------------------------------------------------------
            */

            Select::make('status')
                ->label('Review Status')
                ->options([
                    'draft' => 'Draft',
                    'pending_review' => 'Pending Review',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->disabled()
                ->dehydrated(false),

            TextInput::make('source_type')
                ->label('Source Type')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('ai_model')
                ->label('AI Model')
                ->disabled()
                ->dehydrated(false),

            Textarea::make('generation_notes')
                ->label('Generation / Analysis Notes')
                ->rows(4)
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | QUESTION
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class / Level')
                    ->placeholder('-')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | CLASSIFICATION
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('topic')
                    ->label('Topic')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subtopic')
                    ->label('Subtopic')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('question_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            match ($state) {
                                'multiple_choice' => 'MCQ',
                                'short_answer' => 'Short Answer',
                                'structured' => 'Structured',
                                'matching' => 'Matching',
                                'note_taking' => 'Note Taking',
                                'writing' => 'Writing',
                                'email' => 'Email',
                                'essay' => 'Essay',
                                'true_false' => 'True / False',
                                'fill_blank' => 'Fill Blank',
                                default => str($state)
                                    ->replace('_', ' ')
                                    ->title(),
                            }
                    ),

                Tables\Columns\TextColumn::make('difficulty')
                    ->label('Difficulty')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'easy' => 'success',
                        'medium' => 'warning',
                        'hard' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('marks')
                    ->label('Marks')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | REVIEW STATUS
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('status')
                    ->label('Review Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_review' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            match ($state) {
                                'pending_review' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'draft' => 'Draft',
                                default => str($state)->title(),
                            }
                    ),

                /*
                |--------------------------------------------------------------------------
                | SOURCE
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('pastPaper.title')
                    ->label('Past Paper')
                    ->limit(35)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ai_model')
                    ->label('AI Model')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

            ])

            /*
            |--------------------------------------------------------------------------
            | FILTERS
            |--------------------------------------------------------------------------
            */

            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->label('Review Status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_review' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\SelectFilter::make('question_type')
                    ->label('Question Type')
                    ->options([
                        'multiple_choice' => 'Multiple Choice',
                        'short_answer' => 'Short Answer',
                        'structured' => 'Structured',
                        'matching' => 'Matching',
                        'note_taking' => 'Note Taking',
                        'writing' => 'Writing',
                        'email' => 'Email Writing',
                        'essay' => 'Essay Writing',
                    ]),

                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('Difficulty')
                    ->options([
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                    ]),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class / Level')
                    ->relationship('schoolClass', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('past_paper_id')
                    ->label('Past Paper')
                    ->relationship('pastPaper', 'title')
                    ->searchable()
                    ->preload(),

            ])

            /*
            |--------------------------------------------------------------------------
            | ACTIONS
            |--------------------------------------------------------------------------
            */

            ->actions([

                /*
                |--------------------------------------------------------------------------
                | REVIEW
                |--------------------------------------------------------------------------
                */

                Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(
                        fn (Question $record): string =>
                            static::getUrl('review', ['record' => $record])
                    ),

                    

                    

                /*
                |--------------------------------------------------------------------------
                | EDIT
                |--------------------------------------------------------------------------
                */

                EditAction::make(),

                /*
                |--------------------------------------------------------------------------
                | APPROVE
                |--------------------------------------------------------------------------
                */

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Question')
                    ->modalDescription(
                        'This question will become available in the approved question bank.'
                    )
                    ->modalSubmitActionLabel('Approve Question')
                    ->visible(
                        fn (Question $record): bool =>
                            in_array(
                                $record->status,
                                ['pending_review', 'draft'],
                                true
                            )
                    )
                    ->action(function (Question $record): void {

                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                    }),

                /*
                |--------------------------------------------------------------------------
                | REJECT
                |--------------------------------------------------------------------------
                */

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Question')
                    ->modalDescription(
                        'This question will be marked as rejected and will not be available for student practice.'
                    )
                    ->modalSubmitActionLabel('Reject Question')
                    ->visible(
                        fn (Question $record): bool =>
                            in_array(
                                $record->status,
                                ['pending_review', 'draft'],
                                true
                            )
                    )
                    ->action(function (Question $record): void {

                        $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                    }),

                /*
                |--------------------------------------------------------------------------
                | DELETE
                |--------------------------------------------------------------------------
                */

                DeleteAction::make()
                    ->visible(
                        fn (Question $record): bool =>
                            $record->status !== 'approved'
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | BULK ACTIONS
            |--------------------------------------------------------------------------
            */

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'edit' => EditQuestion::route('/{record}/edit'),
            'review' => ReviewQuestion::route('/{record}/review'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'subject',
                'schoolClass',
                'pastPaper',
                'options',
                'reviewer',
            ]);
    }
}
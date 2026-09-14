<?php

namespace App\Filament\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pastPaper.title')
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->searchable(),
                TextColumn::make('class_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('topic')
                    ->searchable(),
                TextColumn::make('subtopic')
                    ->searchable(),
                TextColumn::make('concept')
                    ->searchable(),
                TextColumn::make('question_type')
                    ->searchable(),
                TextColumn::make('difficulty')
                    ->searchable(),
                TextColumn::make('command_word')
                    ->searchable(),
                TextColumn::make('marks')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('source_type')
                    ->badge(),
                TextColumn::make('sourceQuestion.id')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('reviewed_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ai_model')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

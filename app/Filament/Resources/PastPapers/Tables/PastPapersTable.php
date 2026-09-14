<?php

namespace App\Filament\Resources\PastPapers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PastPapersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject.name')
                    ->searchable(),
                TextColumn::make('class_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uploaded_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('exam_type')
                    ->searchable(),
                TextColumn::make('exam_year')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->searchable(),
                TextColumn::make('file_name')
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
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

<?php

namespace App\Filament\Resources\Broadcasts\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class BroadcastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('target_type')
                    ->label('Audience')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all' => 'All Teachers',
                        'department' => 'Department',
                        'individual' => 'Selected Teachers',
                        default => ucfirst($state),
                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sent By')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not sent'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
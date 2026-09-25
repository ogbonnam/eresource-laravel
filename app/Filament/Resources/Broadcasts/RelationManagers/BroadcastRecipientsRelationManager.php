<?php

namespace App\Filament\Resources\Broadcasts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BroadcastRecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';

    protected static ?string $title = 'Recipient Tracking';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('teacher.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('teacher.faculty.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('read_at')
                    ->label('Status')
                    ->formatStateUsing(
                        fn ($state): string => filled($state)
                            ? 'Viewed'
                            : 'Not Viewed'
                    )
                    ->badge()
                    ->color(
                        fn ($state): string => filled($state)
                            ? 'success'
                            : 'gray'
                    ),

                Tables\Columns\TextColumn::make('first_opened_at')
                    ->label('First Viewed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('last_opened_at')
                    ->label('Last Viewed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('open_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('acknowledged_at')
                    ->label('Acknowledgement')
                    ->formatStateUsing(
                        fn ($state): string => filled($state)
                            ? 'Acknowledged'
                            : 'Not Acknowledged'
                    )
                    ->badge()
                    ->color(
                        fn ($state): string => filled($state)
                            ? 'success'
                            : 'warning'
                    ),

                Tables\Columns\TextColumn::make('acknowledged_at')
                    ->label('Acknowledged At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('last_opened_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('view_status')
                    ->label('View Status')
                    ->options([
                        'viewed' => 'Viewed',
                        'not_viewed' => 'Not Viewed',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'viewed' => $query->whereNotNull('read_at'),

                            'not_viewed' => $query->whereNull('read_at'),

                            default => $query,
                        };
                    }),

                Tables\Filters\SelectFilter::make('acknowledgement')
                    ->label('Acknowledgement')
                    ->options([
                        'acknowledged' => 'Acknowledged',
                        'not_acknowledged' => 'Not Acknowledged',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'acknowledged' =>
                                $query->whereNotNull('acknowledged_at'),

                            'not_acknowledged' =>
                                $query->whereNull('acknowledged_at'),

                            default => $query,
                        };
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
<?php

namespace App\Filament\Resources\Broadcasts;

use App\Filament\Resources\Broadcasts\Pages\CreateBroadcast;
use App\Filament\Resources\Broadcasts\Pages\ListBroadcasts;
use App\Filament\Resources\Broadcasts\Pages\ViewBroadcast;
use App\Filament\Resources\Broadcasts\Schemas\BroadcastForm;
use App\Filament\Resources\Broadcasts\Tables\BroadcastsTable;
use App\Models\Broadcast;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BroadcastResource extends Resource
{
    protected static ?string $model = Broadcast::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|UnitEnum|null $navigationGroup = 'School Management';

    protected static ?string $navigationLabel = 'Teacher Broadcasts';

    protected static ?string $modelLabel = 'Teacher Broadcast';

    protected static ?string $pluralModelLabel = 'Teacher Broadcasts';

    protected static ?int $navigationSort = 25;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return BroadcastForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BroadcastsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'sender',
                'department',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Broadcasts\RelationManagers\BroadcastRecipientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBroadcasts::route('/'),
            'create' => CreateBroadcast::route('/create'),
            'view' => ViewBroadcast::route('/{record}'),
        ];
    }
}
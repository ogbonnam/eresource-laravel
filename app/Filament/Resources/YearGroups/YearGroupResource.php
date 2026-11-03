<?php

namespace App\Filament\Resources\YearGroups;

use App\Filament\Resources\YearGroups\Pages;
use App\Models\YearGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

class YearGroupResource extends Resource
{
    protected static ?string $model = YearGroup::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Year Groups';

    protected static ?string $modelLabel = 'Year Group';

    protected static ?string $pluralModelLabel = 'Year Groups';

    protected static string|\UnitEnum|null $navigationGroup = 'Academic Management';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('name')
                ->label('Year Group Name')
                ->placeholder('e.g. Year 7')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('access_code')
                ->label('Year Group Access Code')
                ->placeholder('Leave blank to generate automatically')
                ->helperText('A unique code will be generated automatically if this field is left blank.')
                ->maxLength(30)
                ->unique(ignoreRecord: true)
                ->dehydrateStateUsing(fn ($state) => filled($state)
                    ? strtoupper(trim($state))
                    : null),

            Toggle::make('require_approval')
                ->label('Require Teacher Approval')
                ->helperText('When enabled, students must wait for approval before accessing courses.')
                ->default(false),

            Toggle::make('is_active')
                ->label('Active')
                ->helperText('Inactive year groups cannot accept new enrollments.')
                ->default(true),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Year Group')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('access_code')
                    ->label('Access Code')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('classes_count')
                    ->label('Assigned Classes')
                    ->counts('classes')
                    ->sortable(),

                TextColumn::make('require_approval')
                    ->label('Approval')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state
                        ? 'Required'
                        : 'Not Required')
                    ->color(fn (bool $state) => $state
                        ? 'warning'
                        : 'success'),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state
                        ? 'Active'
                        : 'Inactive')
                    ->color(fn (bool $state) => $state
                        ? 'success'
                        : 'danger'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),

            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListYearGroups::route('/'),
            'create' => Pages\CreateYearGroup::route('/create'),
            'edit' => Pages\EditYearGroup::route('/{record}/edit'),
        ];
    }
}

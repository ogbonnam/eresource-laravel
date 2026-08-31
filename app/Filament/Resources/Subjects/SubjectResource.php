<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Resources\Subjects\Pages;
use App\Models\Subject;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Subjects';

    protected static ?string $modelLabel = 'Subject';

    protected static ?string $pluralModelLabel = 'Subjects';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Subject Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ignoreRecord: true
                    ),

                TextInput::make('code')
                    ->label('Subject Code')
                    ->required()
                    ->maxLength(50)
                    ->unique(
                        ignoreRecord: true
                    ),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->placeholder('No description'),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string => $state
                            ? 'Active'
                            : 'Inactive'
                    )
                    ->color(
                        fn (bool $state): string => $state
                            ? 'success'
                            : 'gray'
                    ),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All subjects')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('name')

            ->defaultPaginationPageOption(10)

            ->paginated([
                10,
                25,
                50,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
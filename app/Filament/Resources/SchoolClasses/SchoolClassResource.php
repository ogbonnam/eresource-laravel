<?php

namespace App\Filament\Resources\SchoolClasses;

use App\Filament\Resources\SchoolClasses\Pages;
use App\Models\SchoolClass;
use App\Models\Subject;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Classes';

    protected static ?string $modelLabel = 'Class';

    protected static ?string $pluralModelLabel = 'Classes';

    protected static ?int $navigationSort = 10;


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Class Name
                |--------------------------------------------------------------------------
                */

                TextInput::make('name')
                    ->label('Class Name')
                    ->placeholder('e.g. SS2')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ignoreRecord: true
                    ),


                /*
                |--------------------------------------------------------------------------
                | Class Code
                |--------------------------------------------------------------------------
                */

                TextInput::make('code')
                    ->label('Class Code')
                    ->placeholder('e.g. SS2')
                    ->maxLength(50)
                    ->unique(
                        ignoreRecord: true
                    ),


                /*
                |--------------------------------------------------------------------------
                | Description
                |--------------------------------------------------------------------------
                */

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Optional description for this class')
                    ->rows(3)
                    ->maxLength(1000),


                /*
                |--------------------------------------------------------------------------
                | Subjects
                |--------------------------------------------------------------------------
                |
                | This is the important Stage 2 part.
                |
                | Example:
                |
                | SS2
                | ├── Mathematics
                | ├── English
                | ├── Physics
                | └── Chemistry
                |
                */

                Select::make('subjects')
                    ->label('Subjects')
                    ->relationship(
                        name: 'subjects',
                        titleAttribute: 'name'
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->optionsLimit(100)
                    ->placeholder('Select subjects for this class')
                    ->helperText(
                        'Select the subjects that are available to students in this class.'
                    )
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Active
                |--------------------------------------------------------------------------
                */

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText(
                        'Inactive classes will not be available for normal use.'
                    ),

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
                | Class
                |--------------------------------------------------------------------------
                */

                TextColumn::make('name')
                    ->label('Class')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),


                /*
                |--------------------------------------------------------------------------
                | Code
                |--------------------------------------------------------------------------
                */

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),


                /*
                |--------------------------------------------------------------------------
                | Subjects
                |--------------------------------------------------------------------------
                */

                TextColumn::make('subjects.name')
                    ->label('Subjects')
                    ->badge()
                    ->separator(',')
                    ->limitList(4)
                    ->expandableLimitedList()
                    ->placeholder('No subjects assigned'),


                /*
                |--------------------------------------------------------------------------
                | Subject Count
                |--------------------------------------------------------------------------
                */

                TextColumn::make('subjects_count')
                    ->label('Subjects')
                    ->counts('subjects')
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string =>
                            $state ? 'Active' : 'Inactive'
                    )
                    ->color(
                        fn (bool $state): string =>
                            $state ? 'success' : 'gray'
                    ),


                /*
                |--------------------------------------------------------------------------
                | Created
                |--------------------------------------------------------------------------
                */

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])


            /*
            |--------------------------------------------------------------------------
            | FILTERS
            |--------------------------------------------------------------------------
            */

            ->filters([

                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

            ])


            /*
            |--------------------------------------------------------------------------
            | ACTIONS
            |--------------------------------------------------------------------------
            */

            ->actions([
                EditAction::make(),

                DeleteAction::make(),
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


            /*
            |--------------------------------------------------------------------------
            | SORTING
            |--------------------------------------------------------------------------
            */

            ->defaultSort('name')


            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            ->defaultPaginationPageOption(10)

            ->paginated([
                10,
                25,
                50,
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolClasses::route('/'),

            'create' => Pages\CreateSchoolClass::route('/create'),

            'edit' => Pages\EditSchoolClass::route('/{record}/edit'),
        ];
    }
}
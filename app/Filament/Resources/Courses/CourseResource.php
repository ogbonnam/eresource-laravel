<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Filament\Resources\Courses\Pages\ViewCourse;
use App\Models\Course;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Courses';

    protected static ?string $modelLabel = 'Course';

    protected static ?string $pluralModelLabel = 'Courses';

    protected static ?int $navigationSort = 2;


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Course Name
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Course Name')
                    ->required()
                    ->maxLength(255),


                /*
                |--------------------------------------------------------------------------
                | Course Code
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\TextInput::make('code')
                    ->label('Course Code')
                    ->maxLength(100),


                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\Select::make('teacher_id')
                    ->label('Teacher')
                    ->relationship(
                        'teacher',
                        'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),


                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\Select::make('class_id')
                    ->label('Class')
                    ->relationship(
                        'schoolClass',
                        'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),


                /*
                |--------------------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\Select::make('subject_id')
                    ->label('Subject')
                    ->relationship(
                        'subject',
                        'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),


                /*
                |--------------------------------------------------------------------------
                | Description
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->maxLength(5000)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Active
                |--------------------------------------------------------------------------
                */

                \Filament\Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table

            /*
            |--------------------------------------------------------------------------
            | Query
            |--------------------------------------------------------------------------
            |
            | Load the relationships and counts needed by the course
            | administration table.
            |
            */

            ->query(
                Course::query()
                    ->with([
                        'teacher',
                        'schoolClass',
                        'subject',

                        /*
                        |--------------------------------------------------------------------------
                        | Assignments + submission counts
                        |--------------------------------------------------------------------------
                        */

                        'assignments' => function ($query) {
                            $query->withCount('submissions');
                        },

                        /*
                        |--------------------------------------------------------------------------
                        | Resources
                        |--------------------------------------------------------------------------
                        */

                        'resources',
                    ])
                    ->withCount([
                        'students',
                        'resources',
                        'assignments',
                    ])
            )


            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Course
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('name')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(
                        fn (Course $record): ?string =>
                            $record->code
                                ? 'Code: ' . $record->code
                                : null
                    ),


                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | Students
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),


                /*
                |--------------------------------------------------------------------------
                | Resources
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('resources_count')
                    ->label('Resources')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),


                /*
                |--------------------------------------------------------------------------
                | Assignments
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Assignments')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),


                /*
                |--------------------------------------------------------------------------
                | Submissions
                |--------------------------------------------------------------------------
                |
                | Total submissions across all assignments belonging
                | to this course.
                |
                */

                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->state(function (Course $record): int {

                        return $record->assignments
                            ->sum(
                                fn ($assignment) =>
                                    $assignment->submissions_count ?? 0
                            );

                    })
                    ->numeric()
                    ->badge()
                    ->color('primary'),


                /*
                |--------------------------------------------------------------------------
                | Last Activity
                |--------------------------------------------------------------------------
                |
                | Currently considers:
                |
                | - Course creation
                | - Resource updates
                | - Assignment updates
                |
                */

                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->state(function (Course $record): string {

                        $dates = collect([

                            $record->created_at,

                            $record->resources
                                ->max('updated_at'),

                            $record->assignments
                                ->max('updated_at'),

                        ])
                            ->filter()
                            ->map(
                                fn ($date) =>
                                    $date instanceof \Carbon\Carbon
                                        ? $date
                                        : \Carbon\Carbon::parse($date)
                            );

                        if ($dates->isEmpty()) {
                            return 'No activity';
                        }

                        return $dates
                            ->sortDesc()
                            ->first()
                            ->diffForHumans();

                    }),


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string =>
                            $state
                                ? 'Active'
                                : 'Inactive'
                    )
                    ->color(
                        fn (bool $state): string =>
                            $state
                                ? 'success'
                                : 'gray'
                    ),

            ])


            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            ->filters([

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),


                /*
                |--------------------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------------------
                */

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship(
                        'teacher',
                        'name'
                    )
                    ->searchable()
                    ->preload(),


                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                */

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship(
                        'schoolClass',
                        'name'
                    )
                    ->searchable()
                    ->preload(),


                /*
                |--------------------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------------------
                */

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship(
                        'subject',
                        'name'
                    )
                    ->searchable()
                    ->preload(),

            ])


            /*
            |--------------------------------------------------------------------------
            | Actions
            |--------------------------------------------------------------------------
            */

            ->actions([

                /*
                |--------------------------------------------------------------------------
                | View Course
                |--------------------------------------------------------------------------
                |
                | This opens:
                |
                | Courses/{record}
                |
                | which is handled by the custom ViewCourse page.
                |
                */

                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-chart-bar'),


                /*
                |--------------------------------------------------------------------------
                | Existing Edit Action
                |--------------------------------------------------------------------------
                |
                | This remains completely separate from the analytics page.
                |
                */

                EditAction::make(),

            ])


            /*
            |--------------------------------------------------------------------------
            | Default Sorting
            |--------------------------------------------------------------------------
            */

            ->defaultSort(
                'created_at',
                'desc'
            )


            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            ->defaultPaginationPageOption(10)

            ->paginated([
                10,
                25,
                50,
                100,
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Course List
            |--------------------------------------------------------------------------
            */

            'index' =>
                ListCourses::route('/'),


            /*
            |--------------------------------------------------------------------------
            | Create Course
            |--------------------------------------------------------------------------
            */

            'create' =>
                CreateCourse::route('/create'),


            /*
            |--------------------------------------------------------------------------
            | View / Analytics
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | This must come BEFORE the edit route.
            |
            */

            'view' =>
                ViewCourse::route('/{record}'),


            /*
            |--------------------------------------------------------------------------
            | Edit Course
            |--------------------------------------------------------------------------
            */

            'edit' =>
                EditCourse::route('/{record}/edit'),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }
}
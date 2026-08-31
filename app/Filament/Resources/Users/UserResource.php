<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?int $navigationSort = 1;


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignoreRecord: true,
                    )
                    ->maxLength(255),

                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Administrator',
                        'teacher' => 'Teacher',
                        'student' => 'Student',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrated(
                        fn (?string $state): bool => filled($state)
                    )
                    ->dehydrateStateUsing(
                        fn (string $state): string => Hash::make($state)
                    )
                    ->required(
                        fn (string $operation): bool =>
                            $operation === 'create'
                    )
                    ->minLength(8)
                    ->maxLength(255)
                    ->helperText(
                        'Leave blank when editing to keep the current password.'
                    ),

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
            ->query(
                User::query()
                    ->with([
                        'enrollments.course.schoolClass',
                    ])
            )

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Name
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),


                /*
                |--------------------------------------------------------------------------
                | Email
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),


                /*
                |--------------------------------------------------------------------------
                | Role
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'admin' => 'Administrator',
                            'teacher' => 'Teacher',
                            'student' => 'Student',
                            default => ucfirst($state ?? 'Unknown'),
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'admin' => 'danger',
                            'teacher' => 'warning',
                            'student' => 'success',
                            default => 'gray',
                        }
                    )
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | Class
                |--------------------------------------------------------------------------
                |
                | A student can potentially have more than one enrollment.
                | Display active enrolled class/classes.
                |
                */

                Tables\Columns\TextColumn::make('class')
                    ->label('Class')
                    ->searchable()
                    ->state(function (User $record): string {
                        return $record->enrollments
                            ->filter(
                                fn ($enrollment) =>
                                    $enrollment->status === 'active'
                            )
                            ->map(
                                fn ($enrollment) =>
                                    $enrollment->course?->schoolClass?->name
                            )
                            ->filter()
                            ->unique()
                            ->implode(', ') ?: 'Not assigned';
                    }),


                /*
                |--------------------------------------------------------------------------
                | Verified
                |--------------------------------------------------------------------------
                */

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean(),


                /*
                |--------------------------------------------------------------------------
                | Joined
                |--------------------------------------------------------------------------
                */

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->since(),

            ])


            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            ->filters([

                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Administrator',
                        'teacher' => 'Teacher',
                        'student' => 'Student',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Account Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function ($query, array $data) {

                        if (! filled($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->where(
                            'status',
                            $data['value']
                        );
                    }),

            ])


            /*
            |--------------------------------------------------------------------------
            | Actions
            |--------------------------------------------------------------------------
            */

            ->actions([

                /*
                |--------------------------------------------------------------------------
                | Edit
                |--------------------------------------------------------------------------
                |
                | Existing Edit action remains unchanged.
                |
                */

                EditAction::make(),


                /*
                |--------------------------------------------------------------------------
                | Impersonate
                |--------------------------------------------------------------------------
                |
                | Only teachers and students can be impersonated.
                |
                */

                Action::make('impersonate')
                    ->label('Impersonate')
                    ->icon('heroicon-o-user-circle')
                    ->color('warning')

                    /*
                    | Only display this action for teachers/students.
                    */

                    ->visible(
                        fn (User $record): bool =>
                            in_array(
                                $record->role,
                                ['teacher', 'student'],
                                true
                            )
                    )

                    /*
                    | Confirmation dialog.
                    */

                    ->requiresConfirmation()

                    ->modalHeading(
                        fn (User $record): string =>
                            "Impersonate {$record->name}?"
                    )

                    ->modalDescription(
                        fn (User $record): string =>
                            "You will be logged in as {$record->name}. " .
                            "You can return to your administrator account " .
                            "using the Stop Impersonation option."
                    )

                    ->modalSubmitActionLabel(
                        'Yes, impersonate user'
                    )

                    ->modalCancelActionLabel(
                        'Cancel'
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Execute impersonation
                    |--------------------------------------------------------------------------
                    |
                    | The actual authentication switch is handled by
                    | ImpersonationController.
                    |
                    */

                    ->url(
                        fn (User $record): string =>
                            route(
                                'admin.impersonate.start',
                                ['user' => $record]
                            )
                    )

                    /*
                    | IMPORTANT:
                    |
                    | The controller expects POST.
                    |
                    */

                    ->extraAttributes([
                        'formmethod' => 'POST',
                    ]),

            ])


            /*
            |--------------------------------------------------------------------------
            | Sorting
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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

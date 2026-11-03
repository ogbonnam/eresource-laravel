<?php

namespace App\Filament\Resources\YearGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class YearGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('access_code')
                    ->required(),
                Toggle::make('require_approval')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}

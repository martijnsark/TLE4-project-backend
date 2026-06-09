<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('username')
                ->disabled()
                ->maxLength(50),

            TextInput::make('name')
                ->disabled()
                ->maxLength(100),

            TextInput::make('email')
                ->label('E-mailadres')
                ->email()
                ->disabled(),

            Select::make('role')
                ->options([
                    'user'  => 'Gebruiker',
                    'admin' => 'Admin',
                ])
                ->required()
                ->default('user'),
        ]);
    }
}

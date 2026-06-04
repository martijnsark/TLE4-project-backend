<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('username')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),

            TextInput::make('name')
                ->maxLength(100),

            TextInput::make('email')
                ->label('E-mailadres')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            Select::make('role')
                ->options([
                    'user'  => 'Gebruiker',
                    'admin' => 'Admin',
                ])
                ->required()
                ->default('user'),

            TextInput::make('password')
                ->password()
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->helperText('Laat leeg om het wachtwoord niet te wijzigen'),
        ]);
    }
}

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
                ->hint('alleen-lezen')
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Identiteitsvelden kan alleen de gebruiker zelf wijzigen. Admins beheren via dit form alleen de rol.')
                ->maxLength(50),

            TextInput::make('name')
                ->label('Naam')
                ->disabled()
                ->hint('alleen-lezen')
                ->maxLength(100),

            TextInput::make('email')
                ->label('E-mailadres')
                ->email()
                ->disabled()
                ->hint('alleen-lezen'),

            Select::make('role')
                ->label('Rol')
                ->options([
                    'user'  => 'Gebruiker',
                    'admin' => 'Admin',
                ])
                ->required()
                ->default('user')
                ->native(false)
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Bepaalt of deze gebruiker toegang heeft tot het admin-paneel.'),
        ]);
    }
}

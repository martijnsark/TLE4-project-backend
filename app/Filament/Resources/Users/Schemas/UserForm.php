<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
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
                ->maxLength(50)
                ->unique(table: User::class, ignoreRecord: true)
                ->disabled(fn (string $operation): bool => $operation === 'edit')
                ->hint(fn (string $operation): ?string => $operation === 'edit' ? 'alleen-lezen' : null)
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Username is gekoppeld aan login + URL-slugs en kan na create niet meer worden gewijzigd.'),

            TextInput::make('name')
                ->label('Naam')
                ->required()
                ->maxLength(100),

            TextInput::make('email')
                ->label('E-mailadres')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(table: User::class, ignoreRecord: true),

            TextInput::make('password')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->hint(fn (string $operation): ?string => $operation === 'edit' ? 'leeg laten om niet te wijzigen' : null)
                ->minLength(8)
                ->maxLength(255),

            Select::make('role')
                ->label('Rol')
                ->options([
                    'user'  => 'Gebruiker',
                    'admin' => 'Admin',
                ])
                ->required()
                ->default('user')
                ->native(false)
                ->disabled(fn (?User $record): bool => $record !== null && $record->id === auth()->id())
                ->hint(fn (?User $record): ?string => $record !== null && $record->id === auth()->id() ? 'je eigen rol kun je niet wijzigen' : null)
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Bepaalt of deze gebruiker toegang heeft tot het admin-paneel. Je kunt je eigen rol niet wijzigen — anders sluit je jezelf uit.'),
        ]);
    }
}

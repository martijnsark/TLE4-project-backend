<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(100),

                Select::make('category')
                    ->label('Categorie')
                    ->options([
                        'navigation' => 'Navigation (hoofdcategorie in RN-app)',
                        'topic'      => 'Topic (inhoudelijk onderwerp)',
                        'flag'       => 'Flag (Trending, Goed nieuws, …)',
                    ])
                    ->required()
                    ->default('topic')
                    ->native(false)
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Bepaalt waar de tag opduikt: navigation = hoofdmenu, topic = filter, flag = badge.'),
            ]);
    }
}

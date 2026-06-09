<?php

namespace App\Filament\Resources\Sources\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->required()
                    ->url()
                    ->maxLength(255),
                TextInput::make('reliability_score')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->helperText('1 t/m 100, hoger = betrouwbaarder'),
            ]);
    }
}

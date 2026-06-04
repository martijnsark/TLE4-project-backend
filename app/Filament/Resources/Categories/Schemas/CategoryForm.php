<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->maxLength(100),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Auto-ingevuld op basis van naam'),

            ColorPicker::make('color')
                ->label('Kleur')
                ->nullable(),

            TextInput::make('position')
                ->label('Volgorde')
                ->numeric()
                ->default(0)
                ->minValue(0),
        ]);
    }
}

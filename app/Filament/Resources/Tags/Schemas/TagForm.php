<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Enums\TagCategory;
use App\Enums\TagIcon;
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
                    ->options(collect(TagCategory::cases())->mapWithKeys(fn (TagCategory $c) => [$c->value => $c->label()])->all())
                    ->required()
                    ->default(TagCategory::Topic->value)
                    ->native(false)
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Bepaalt waar de tag opduikt: navigation = hoofdmenu, topic = filter, flag = badge.'),

                Select::make('icon')
                    ->label('Icoon')
                    ->options(TagIcon::options())
                    ->searchable()
                    ->native(false)
                    ->placeholder('Geen icoon')
                    ->helperText('Naam van het icoon zoals de app het rendert (impakt-rn). Optioneel.'),
            ]);
    }
}

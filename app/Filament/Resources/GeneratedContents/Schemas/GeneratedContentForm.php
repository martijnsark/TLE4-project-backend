<?php

namespace App\Filament\Resources\GeneratedContents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GeneratedContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('generated_text')
                ->label('Gegenereerde tekst')
                ->required()
                ->rows(8)
                ->columnSpanFull(),

            TextInput::make('original_news_url')
                ->label('Originele nieuwslink')
                ->url()
                ->maxLength(500)
                ->columnSpanFull(),

            Select::make('status')
                ->options([
                    'draft'    => 'Concept',
                    'inactive' => 'Inactief',
                    'active'   => 'Actief',
                    'archived' => 'Gearchiveerd',
                ])
                ->required()
                ->default('draft'),
        ]);
    }
}

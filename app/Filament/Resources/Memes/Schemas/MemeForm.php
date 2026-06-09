<?php

namespace App\Filament\Resources\Memes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('article_id')
                ->label('Gekoppeld artikel')
                ->relationship('article', 'title')
                ->searchable()
                ->preload()
                ->nullable()
                ->columnSpanFull(),

            FileUpload::make('image_url')
                ->label('Afbeelding (img)')
                ->disk('public')
                ->directory('memes')
                ->image()
                ->imagePreviewHeight('200')
                ->required()
                ->columnSpanFull(),

            TextInput::make('title')
                ->label('Intern label (optioneel)')
                ->maxLength(255),

            TextInput::make('author')
                ->label('Auteur (handle)')
                ->maxLength(100),

            TextInput::make('author_name')
                ->label('Auteursnaam')
                ->maxLength(100),

            TextInput::make('cat')
                ->label('Categorie (UPPERCASE)')
                ->maxLength(50),

            Textarea::make('top')
                ->label('Bovenste tekst')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('bot')
                ->label('Onderste tekst')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('caption')
                ->label('Bijschrift')
                ->columnSpanFull(),
        ]);
    }
}

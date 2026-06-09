<?php

namespace App\Filament\Resources\Memes\Schemas;

use App\Models\Tag;
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

            Select::make('cat')
                ->label('Categorie')
                ->options(fn () => Tag::where('category', 'navigation')
                    ->pluck('name', 'name')
                    ->mapWithKeys(fn ($label, $key) => [strtoupper($key) => $label])
                    ->all())
                ->searchable()
                ->nullable()
                ->helperText('Gebaseerd op de navigation-tags. Opgeslagen als UPPERCASE.'),

            Textarea::make('top')
                ->label('Bovenste tekst')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('bot')
                ->label('Onderste tekst')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }
}

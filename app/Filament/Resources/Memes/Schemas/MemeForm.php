<?php

namespace App\Filament\Resources\Memes\Schemas;

use App\Models\Tag;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                ->hint('optioneel')
                ->columnSpanFull(),

            Select::make('editor_id')
                ->label('Redacteur')
                ->relationship('editor', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'De redacteur die deze meme heeft toegevoegd. Wordt automatisch ingevuld met de ingelogde admin.')
                ->columnSpanFull(),

            FileUpload::make('image_url')
                ->label('Afbeelding')
                ->disk('public')
                ->directory('memes')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(2048)
                ->imagePreviewHeight('200')
                ->required()
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'API-veld: img. Toegestane formats: JPG, PNG, WebP. Max 2 MB.')
                ->columnSpanFull(),

            TextInput::make('title')
                ->label('Intern label')
                ->hint('optioneel')
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Alleen zichtbaar in het admin-paneel. Wordt gebruikt om memes terug te vinden via de zoekbalk in de memes-lijst (naast top/onder-tekst, credit en artikel-titel).')
                ->maxLength(255),

            Select::make('cat')
                ->label('Categorie')
                ->options(fn () => Tag::where('category', 'navigation')
                    ->pluck('name', 'name')
                    ->mapWithKeys(fn ($label, $key) => [strtoupper($key) => $label])
                    ->all())
                ->searchable()
                ->nullable()
                ->hint('optioneel')
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Gebaseerd op de navigation-tags. Opgeslagen als UPPERCASE.'),

            Textarea::make('top')
                ->label('Bovenste tekst')
                ->hint('optioneel')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('bot')
                ->label('Onderste tekst')
                ->hint('optioneel')
                ->rows(3)
                ->columnSpanFull(),

            Section::make('Externe credit')
                ->description('Optionele credit voor de originele meme-maker (Instagram, X, etc.).')
                ->columns(2)
                ->schema([
                    TextInput::make('author')
                        ->label('Handle')
                        ->placeholder('@klimaatkat')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Social-handle van de originele meme-maker.')
                        ->maxLength(100),

                    TextInput::make('author_name')
                        ->label('Naam')
                        ->placeholder('Sara de Vries')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Volledige naam van de originele meme-maker.')
                        ->maxLength(100),
                ])
                ->columnSpanFull(),
        ]);
    }
}

<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('summary')
                ->label('Samenvatting (sub)')
                ->maxLength(280)
                ->columnSpanFull(),

            Repeater::make('body_paragraphs')
                ->label('Paragrafen (body)')
                ->schema([
                    Textarea::make('value')
                        ->label('Paragraaf')
                        ->required()
                        ->rows(3),
                ])
                ->reorderable()
                ->defaultItems(1)
                ->columnSpanFull(),

            FileUpload::make('image_url')
                ->label('Afbeelding (img)')
                ->disk('public')
                ->directory('articles')
                ->image()
                ->imagePreviewHeight('200')
                ->columnSpanFull(),

            TextInput::make('original_url')
                ->label('Originele URL')
                ->url()
                ->maxLength(500),

            Select::make('category_id')
                ->label('Categorie')
                ->relationship('category', 'name')
                ->searchable()
                ->preload(),

            Select::make('tone')
                ->label('Toon')
                ->options([
                    'Live'        => 'Live',
                    'Achtergrond' => 'Achtergrond',
                    'Reportage'   => 'Reportage',
                    'Opinie'      => 'Opinie',
                ])
                ->required()
                ->default('Achtergrond'),

            Select::make('status')
                ->label('Status')
                ->options([
                    'draft'    => 'Concept',
                    'inactive' => 'Inactief',
                    'active'   => 'Actief',
                    'archived' => 'Gearchiveerd',
                ])
                ->required()
                ->default('draft'),

            Toggle::make('is_good_news')
                ->label('Goed nieuws'),

            Toggle::make('is_trending')
                ->label('Trending'),

            DateTimePicker::make('published_at')
                ->label('Publicatiedatum'),

            Select::make('author_id')
                ->label('Auteur')
                ->relationship('author', 'name')
                ->searchable()
                ->preload(),

            Select::make('tags')
                ->label('Tags')
                ->multiple()
                ->relationship('tags', 'name')
                ->searchable()
                ->preload()
                ->columnSpanFull(),
        ]);
    }
}

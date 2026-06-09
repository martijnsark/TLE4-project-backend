<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Publicatie')
                ->description('Bepaalt of en wanneer dit artikel zichtbaar is in de app.')
                ->columns(3)
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'    => 'Concept',
                            'inactive' => 'Inactief',
                            'active'   => 'Actief',
                            'archived' => 'Gearchiveerd',
                        ])
                        ->required()
                        ->default('draft')
                        ->native(false),

                    DateTimePicker::make('published_at')
                        ->label('Publicatiedatum'),

                    Select::make('author_id')
                        ->label('Auteur')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload(),
                ])
                ->columnSpanFull(),

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
                        ->rows(3),
                ])
                ->reorderable()
                ->defaultItems(0)
                ->addActionLabel('Paragraaf toevoegen')
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

            Select::make('tags')
                ->label('Tags')
                ->multiple()
                ->relationship('tags', 'name')
                ->searchable()
                ->preload()
                ->columnSpanFull(),

            Section::make('Call to Action')
                ->description('Optionele knop onderaan het artikel.')
                ->relationship('callToAction')
                ->schema([
                    TextInput::make('title')
                        ->label('Titel CTA')
                        ->maxLength(255),
                    TextInput::make('target_url')
                        ->label('Doel-URL')
                        ->url()
                        ->maxLength(500),
                    Textarea::make('context_text')
                        ->label('Context')
                        ->rows(2)
                        ->columnSpanFull(),
                    Textarea::make('goal_text')
                        ->label('Doel-tekst')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed()
                ->columnSpanFull(),

            Section::make('Poll')
                ->description('Maximaal één poll per artikel.')
                ->schema([
                    Repeater::make('polls')
                        ->relationship('polls')
                        ->hiddenLabel()
                        ->schema([
                            TextInput::make('question')
                                ->label('Vraag')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Repeater::make('options')
                                ->relationship('options')
                                ->label('Antwoorden')
                                ->schema([
                                    TextInput::make('option_text')
                                        ->label('Antwoord')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->defaultItems(2)
                                ->minItems(2)
                                ->reorderable()
                                ->columnSpanFull(),
                        ])
                        ->defaultItems(0)
                        ->maxItems(1)
                        ->addActionLabel('Poll toevoegen'),
                ])
                ->collapsible()
                ->collapsed()
                ->columnSpanFull(),
        ]);
    }
}

<?php

namespace App\Filament\Resources\Articles\RelationManagers;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CallToActionRelationManager extends RelationManager
{
    protected static string $relationship = 'callToAction';

    protected static ?string $title = 'Call to Action';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedMegaphone;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('context_text')
                ->columnSpanFull(),

            Textarea::make('goal_text')
                ->columnSpanFull(),

            TextInput::make('target_url')
                ->url()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('target_url')->limit(60),
            ])
            ->toolbarActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

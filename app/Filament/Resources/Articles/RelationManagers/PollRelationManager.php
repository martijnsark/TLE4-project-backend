<?php

namespace App\Filament\Resources\Articles\RelationManagers;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PollRelationManager extends RelationManager
{
    protected static string $relationship = 'polls';

    protected static ?string $title = 'Poll';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedChartBar;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('question')
                ->required()
                ->columnSpanFull(),

            Repeater::make('options')
                ->relationship()
                ->schema([
                    TextInput::make('option_text')
                        ->required(),
                ])
                ->defaultItems(0)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question'),
                TextColumn::make('options_count')
                    ->counts('options')
                    ->badge(),
            ])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

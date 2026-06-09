<?php

namespace App\Filament\Resources\Articles\RelationManagers;

use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SourceRelationManager extends RelationManager
{
    protected static string $relationship = 'sources';

    protected static ?string $title = 'Bronnen';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedLink;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('source_url')
                ->label('Bron-URL')
                ->url()
                ->maxLength(500),

            Toggle::make('is_primary')
                ->label('Primaire bron'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Naam'),
                TextColumn::make('pivot.source_url')
                    ->label('Bron-URL')
                    ->limit(60),
                IconColumn::make('pivot.is_primary')
                    ->label('Primair')
                    ->boolean(),
            ])
            ->toolbarActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('source_url')
                            ->label('Bron-URL')
                            ->url()
                            ->maxLength(500),
                        Toggle::make('is_primary')
                            ->label('Primaire bron'),
                    ]),
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ]);
    }
}

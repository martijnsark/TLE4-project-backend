<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Img')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label('Categorie')
                    ->badge(),
                TextColumn::make('tone')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'   => 'success',
                        'draft'    => 'gray',
                        'inactive' => 'warning',
                        'archived' => 'danger',
                    }),
                IconColumn::make('is_good_news')
                    ->label('Goed nieuws')
                    ->boolean(),
                IconColumn::make('is_trending')
                    ->label('Trending')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Categorie')
                    ->relationship('category', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'draft'    => 'Concept',
                        'inactive' => 'Inactief',
                        'active'   => 'Actief',
                        'archived' => 'Gearchiveerd',
                    ]),
                SelectFilter::make('tone')
                    ->options([
                        'Live'        => 'Live',
                        'Achtergrond' => 'Achtergrond',
                        'Reportage'   => 'Reportage',
                        'Opinie'      => 'Opinie',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publiceren')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'active']))
                        ->requiresConfirmation(),
                    BulkAction::make('unpublish')
                        ->label('Depubliceren')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive']))
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

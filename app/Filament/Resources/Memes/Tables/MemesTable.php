<?php

namespace App\Filament\Resources\Memes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('article_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Intern label')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('top', 'like', "%{$search}%")
                        ->orWhere('bot', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('author_name', 'like', "%{$search}%")
                        ->orWhereHas('article', fn (Builder $q) => $q->where('title', 'like', "%{$search}%"))),
                ImageColumn::make('image_url'),
                TextColumn::make('editor.name')
                    ->label('Redacteur')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('author')
                    ->label('Externe credit')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

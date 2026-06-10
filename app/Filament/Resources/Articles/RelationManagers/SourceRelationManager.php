<?php

namespace App\Filament\Resources\Articles\RelationManagers;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SourceRelationManager extends RelationManager
{
    protected static string $relationship = 'sources';

    protected static ?string $title = 'Bronnen';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedLink;

    // Disable lazy-mount so the manager renders inline (we nested it inside the
    // form footer in EditArticle, and the lazy placeholder doesn't get filled
    // when nested — see app/Filament/Resources/Articles/Pages/EditArticle.php).
    protected static bool $isLazy = false;

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->sources()->count();

        return $count === 0 ? 'Ontbreekt' : (string) $count;
    }

    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->sources()->count() === 0 ? 'danger' : 'success';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Naam bron')
                ->required()
                ->maxLength(255),

            TextInput::make('url')
                ->label('Hoofd-URL bron')
                ->url()
                ->required()
                ->maxLength(255),

            TextInput::make('reliability_score')
                ->label('Betrouwbaarheid (1-100)')
                ->numeric()
                ->minValue(1)
                ->maxValue(100),

            TextInput::make('source_url')
                ->label('Bron-URL voor dit artikel')
                ->url()
                ->required()
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
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

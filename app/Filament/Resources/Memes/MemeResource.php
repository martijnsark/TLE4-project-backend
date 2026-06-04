<?php

namespace App\Filament\Resources\Memes;

use App\Filament\Resources\Memes\Pages\CreateMeme;
use App\Filament\Resources\Memes\Pages\EditMeme;
use App\Filament\Resources\Memes\Pages\ListMemes;
use App\Filament\Resources\Memes\Schemas\MemeForm;
use App\Filament\Resources\Memes\Tables\MemesTable;
use App\Models\Meme;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MemeResource extends Resource
{
    protected static ?string $model = Meme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MemeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemes::route('/'),
            'create' => CreateMeme::route('/create'),
            'edit' => EditMeme::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\GeneratedContents;

use App\Filament\Resources\GeneratedContents\Pages\CreateGeneratedContent;
use App\Filament\Resources\GeneratedContents\Pages\EditGeneratedContent;
use App\Filament\Resources\GeneratedContents\Pages\ListGeneratedContents;
use App\Filament\Resources\GeneratedContents\Schemas\GeneratedContentForm;
use App\Filament\Resources\GeneratedContents\Tables\GeneratedContentsTable;
use App\Models\GeneratedContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeneratedContentResource extends Resource
{
    protected static ?string $model = GeneratedContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GeneratedContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeneratedContentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeneratedContents::route('/'),
            'create' => CreateGeneratedContent::route('/create'),
            'edit' => EditGeneratedContent::route('/{record}/edit'),
        ];
    }
}

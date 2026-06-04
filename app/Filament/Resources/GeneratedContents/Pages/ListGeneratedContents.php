<?php

namespace App\Filament\Resources\GeneratedContents\Pages;

use App\Filament\Resources\GeneratedContents\GeneratedContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGeneratedContents extends ListRecords
{
    protected static string $resource = GeneratedContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\GeneratedContents\Pages;

use App\Filament\Resources\GeneratedContents\GeneratedContentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGeneratedContent extends EditRecord
{
    protected static string $resource = GeneratedContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Memes\Pages;

use App\Filament\Resources\Memes\MemeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeme extends CreateRecord
{
    protected static string $resource = MemeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['editor_id'] = $data['editor_id'] ?? auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

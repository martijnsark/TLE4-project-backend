<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->pruneEmptyCallToAction();
    }

    // Layout: keep RelationManagers (Bronnen) tussen het form en de Save-knop,
    // mét de Save-knop binnen de <form> zodat submit werkt.
    //
    // Twee samenhangende ingrepen:
    //   1. getFormContentComponent() — RelationManagers + form actions allebei
    //      in de Form `footer` slot. Filament's default zet alleen de actions
    //      in de footer; door RelationManagers daar OOK te plaatsen schuiven
    //      ze visueel boven Save.
    //   2. content() — overgeschreven om de default sibling-render van
    //      RelationManagers weg te halen (anders rendert Bronnen dubbel).
    //
    // Verder vereist: SourceRelationManager `protected static bool $isLazy = false`
    // anders blijft de placeholder leeg (lazy-mount fires niet binnen Form).

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler($this->getSubmitFormLivewireMethodName())
            ->footer([
                $this->getRelationManagersContentComponent(),
                $this->getFormActionsContentComponent(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }
}

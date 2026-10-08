<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\TranslationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTranslation extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\FAQResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFAQ extends ListRecords
{
    protected static string $resource = FAQResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

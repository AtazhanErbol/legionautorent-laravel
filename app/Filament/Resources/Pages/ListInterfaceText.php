<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\InterfaceTextResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInterfaceText extends ListRecords
{
    protected static string $resource = InterfaceTextResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

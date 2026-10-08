<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\MenuLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMenuLink extends ListRecords
{
    protected static string $resource = MenuLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

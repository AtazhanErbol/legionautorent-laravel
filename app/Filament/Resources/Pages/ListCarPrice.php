<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarPriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarPrice extends ListRecords
{
    protected static string $resource = CarPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

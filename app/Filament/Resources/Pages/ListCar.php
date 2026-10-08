<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCar extends ListRecords
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

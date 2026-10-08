<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarBrandResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarBrand extends ListRecords
{
    protected static string $resource = CarBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

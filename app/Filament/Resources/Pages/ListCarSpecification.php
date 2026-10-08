<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarSpecificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarSpecification extends ListRecords
{
    protected static string $resource = CarSpecificationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

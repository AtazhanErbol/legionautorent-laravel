<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarDiscountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarDiscount extends ListRecords
{
    protected static string $resource = CarDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

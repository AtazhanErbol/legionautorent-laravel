<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarImage extends ListRecords
{
    protected static string $resource = CarImageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

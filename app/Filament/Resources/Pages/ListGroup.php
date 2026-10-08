<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGroup extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

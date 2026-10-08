<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarResource;
use Filament\Resources\Pages\EditRecord;

class EditCar extends EditRecord
{
    protected static string $resource = CarResource::class;
}

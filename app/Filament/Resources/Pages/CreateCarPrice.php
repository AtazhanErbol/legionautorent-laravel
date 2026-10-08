<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarPriceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCarPrice extends CreateRecord
{
    protected static string $resource = CarPriceResource::class;
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarPriceResource;
use Filament\Resources\Pages\EditRecord;

class EditCarPrice extends EditRecord
{
    protected static string $resource = CarPriceResource::class;
}

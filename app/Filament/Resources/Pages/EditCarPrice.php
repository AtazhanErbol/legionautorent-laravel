<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarPriceResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditCarPrice extends EditRecord
{
    protected static string $resource = CarPriceResource::class;
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarBrandResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditCarBrand extends EditRecord
{
    protected static string $resource = CarBrandResource::class;
}

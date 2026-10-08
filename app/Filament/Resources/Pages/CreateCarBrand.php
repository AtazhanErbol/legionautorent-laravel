<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarBrandResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateCarBrand extends CreateRecord
{
    protected static string $resource = CarBrandResource::class;
}

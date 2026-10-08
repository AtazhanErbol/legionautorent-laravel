<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarCategoryResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateCarCategory extends CreateRecord
{
    protected static string $resource = CarCategoryResource::class;
}

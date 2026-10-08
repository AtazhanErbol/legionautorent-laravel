<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarCategoryResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditCarCategory extends EditRecord
{
    protected static string $resource = CarCategoryResource::class;
}

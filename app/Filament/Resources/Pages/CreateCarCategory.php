<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCarCategory extends CreateRecord
{
    protected static string $resource = CarCategoryResource::class;
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}

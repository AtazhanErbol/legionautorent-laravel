<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\ContentBlockResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateContentBlock extends CreateRecord
{
    protected static string $resource = ContentBlockResource::class;
}

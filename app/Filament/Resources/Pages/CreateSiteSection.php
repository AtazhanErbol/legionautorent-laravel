<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;
use App\Filament\Resources\SiteSectionResource;

class CreateSiteSection extends CreateRecord
{
    protected static string $resource = SiteSectionResource::class;
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;
use App\Filament\Resources\SiteSettingsResource;

class CreateSiteSettings extends CreateRecord
{
    protected static string $resource = SiteSettingsResource::class;
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;
use App\Filament\Resources\SiteSettingsResource;

class EditSiteSettings extends EditRecord
{
    protected static string $resource = SiteSettingsResource::class;
}

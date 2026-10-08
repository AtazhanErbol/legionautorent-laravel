<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\SiteSettingsResource;
use Filament\Resources\Pages\EditRecord;

class EditSiteSettings extends EditRecord
{
    protected static string $resource = SiteSettingsResource::class;
}

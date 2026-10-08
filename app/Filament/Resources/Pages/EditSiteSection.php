<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;
use App\Filament\Resources\SiteSectionResource;

class EditSiteSection extends EditRecord
{
    protected static string $resource = SiteSectionResource::class;
}

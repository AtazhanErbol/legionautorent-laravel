<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\ContentBlockResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditContentBlock extends EditRecord
{
    protected static string $resource = ContentBlockResource::class;
}

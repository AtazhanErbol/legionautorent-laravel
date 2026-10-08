<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;
use App\Filament\Resources\RedirectResource;

class CreateRedirect extends CreateRecord
{
    protected static string $resource = RedirectResource::class;
}

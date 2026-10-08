<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;
use App\Filament\Resources\RedirectResource;

class EditRedirect extends EditRecord
{
    protected static string $resource = RedirectResource::class;
}

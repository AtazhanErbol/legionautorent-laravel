<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\MenuLinkResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditMenuLink extends EditRecord
{
    protected static string $resource = MenuLinkResource::class;
}

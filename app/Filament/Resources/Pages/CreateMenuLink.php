<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\MenuLinkResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateMenuLink extends CreateRecord
{
    protected static string $resource = MenuLinkResource::class;
}

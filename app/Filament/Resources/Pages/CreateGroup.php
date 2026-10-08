<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\GroupResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;
}

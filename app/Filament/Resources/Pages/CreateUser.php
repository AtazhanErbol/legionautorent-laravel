<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;
use App\Filament\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

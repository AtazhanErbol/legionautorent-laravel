<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;
use App\Filament\Resources\UserResource;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}

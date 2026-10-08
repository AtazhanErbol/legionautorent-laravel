<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;
use App\Filament\Resources\TranslationResource;

class CreateTranslation extends CreateRecord
{
    protected static string $resource = TranslationResource::class;
}

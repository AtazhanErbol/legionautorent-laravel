<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;
use App\Filament\Resources\TranslationResource;

class EditTranslation extends EditRecord
{
    protected static string $resource = TranslationResource::class;
}

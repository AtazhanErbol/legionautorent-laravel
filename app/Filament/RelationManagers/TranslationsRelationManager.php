<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\TranslationResource;

class TranslationsRelationManager extends CmsRelationManager
{
    protected static string $relationship = 'translations';

    protected static ?string $relatedResource = TranslationResource::class;

    protected static ?string $title = 'Переводы KZ / EN';
}

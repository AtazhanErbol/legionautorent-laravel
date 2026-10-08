<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\CarImageResource;

class PhotosRelationManager extends CmsRelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $relatedResource = CarImageResource::class;

    protected static ?string $title = 'Фотографии';
}

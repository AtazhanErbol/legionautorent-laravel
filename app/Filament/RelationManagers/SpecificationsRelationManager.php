<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\CarSpecificationResource;

class SpecificationsRelationManager extends CmsRelationManager
{
    protected static string $relationship = 'extra_specs';

    protected static ?string $relatedResource = CarSpecificationResource::class;

    protected static ?string $title = 'Характеристики';
}

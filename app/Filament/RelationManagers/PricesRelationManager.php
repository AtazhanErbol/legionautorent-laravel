<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\CarPriceResource;

class PricesRelationManager extends CmsRelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $relatedResource = CarPriceResource::class;

    protected static ?string $title = 'Тарифы';
}

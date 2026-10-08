<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\CarDiscountResource;

class DiscountsRelationManager extends CmsRelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $relatedResource = CarDiscountResource::class;

    protected static ?string $title = 'Скидки';
}

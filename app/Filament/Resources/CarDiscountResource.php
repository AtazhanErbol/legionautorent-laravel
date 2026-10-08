<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarDiscount;
use App\Filament\Resources\Pages\EditCarDiscount;
use App\Filament\Resources\Pages\ListCarDiscount;
use App\Models\CarDiscount;

class CarDiscountResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarDiscount::class;

    protected static ?string $slug = 'discounts';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Скидка';

    protected static ?string $pluralModelLabel = 'Скидки';

    protected static ?int $navigationSort = 4;

    public static function getPages(): array
    {
        return ['index' => ListCarDiscount::route('/'), 'create' => CreateCarDiscount::route('/create'), 'edit' => EditCarDiscount::route('/{record}/edit')];
    }
}

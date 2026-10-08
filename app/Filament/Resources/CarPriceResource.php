<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarPrice;
use App\Filament\Resources\Pages\EditCarPrice;
use App\Filament\Resources\Pages\ListCarPrice;
use App\Models\CarPrice;

class CarPriceResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarPrice::class;

    protected static ?string $slug = 'tariffs';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Тариф';

    protected static ?string $pluralModelLabel = 'Тарифы';

    protected static ?int $navigationSort = 3;

    public static function getPages(): array
    {
        return ['index' => ListCarPrice::route('/'), 'create' => CreateCarPrice::route('/create'), 'edit' => EditCarPrice::route('/{record}/edit')];
    }
}

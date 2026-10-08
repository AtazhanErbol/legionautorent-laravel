<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarBrand;
use App\Filament\Resources\Pages\EditCarBrand;
use App\Filament\Resources\Pages\ListCarBrand;
use App\Models\CarBrand;

class CarBrandResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarBrand::class;

    protected static ?string $slug = 'brands';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Марка';

    protected static ?string $pluralModelLabel = 'Марки автомобилей';

    protected static ?int $navigationSort = 7;

    public static function getPages(): array
    {
        return ['index' => ListCarBrand::route('/'), 'create' => CreateCarBrand::route('/create'), 'edit' => EditCarBrand::route('/{record}/edit')];
    }
}

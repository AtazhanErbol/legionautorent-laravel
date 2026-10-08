<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarCategory;
use App\Filament\Resources\Pages\EditCarCategory;
use App\Filament\Resources\Pages\ListCarCategory;
use App\Models\CarCategory;

class CarCategoryResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarCategory::class;

    protected static ?string $slug = 'categories';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Класс автомобиля';

    protected static ?string $pluralModelLabel = 'Классы автомобилей';

    protected static ?int $navigationSort = 6;

    public static function getPages(): array
    {
        return ['index' => ListCarCategory::route('/'), 'create' => CreateCarCategory::route('/create'), 'edit' => EditCarCategory::route('/{record}/edit')];
    }
}

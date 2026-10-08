<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCar;
use App\Filament\Resources\Pages\EditCar;
use App\Filament\Resources\Pages\ListCar;
use App\Models\Car;

class CarResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = Car::class;

    protected static ?string $slug = 'cars';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Автомобиль';

    protected static ?string $pluralModelLabel = 'Автомобили';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return ['index' => ListCar::route('/'), 'create' => CreateCar::route('/create'), 'edit' => EditCar::route('/{record}/edit')];
    }
}

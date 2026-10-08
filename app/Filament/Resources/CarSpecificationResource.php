<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarSpecification;
use App\Filament\Resources\Pages\EditCarSpecification;
use App\Filament\Resources\Pages\ListCarSpecification;
use App\Models\CarSpecification;

class CarSpecificationResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarSpecification::class;

    protected static ?string $slug = 'specifications';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Характеристика';

    protected static ?string $pluralModelLabel = 'Характеристики';

    protected static ?int $navigationSort = 5;

    public static function getPages(): array
    {
        return ['index' => ListCarSpecification::route('/'), 'create' => CreateCarSpecification::route('/create'), 'edit' => EditCarSpecification::route('/{record}/edit')];
    }
}

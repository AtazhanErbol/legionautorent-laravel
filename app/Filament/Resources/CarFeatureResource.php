<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarFeature;
use App\Filament\Resources\Pages\EditCarFeature;
use App\Filament\Resources\Pages\ListCarFeature;
use App\Models\CarFeature;

class CarFeatureResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarFeature::class;

    protected static ?string $slug = 'features';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Оснащение';

    protected static ?string $pluralModelLabel = 'Оснащение';

    protected static ?int $navigationSort = 8;

    public static function getPages(): array
    {
        return ['index' => ListCarFeature::route('/'), 'create' => CreateCarFeature::route('/create'), 'edit' => EditCarFeature::route('/{record}/edit')];
    }
}

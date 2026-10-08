<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCity;
use App\Filament\Resources\Pages\EditCity;
use App\Filament\Resources\Pages\ListCity;
use App\Models\City;

class CityResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = City::class;

    protected static ?string $slug = 'cities';

    protected static string|\UnitEnum|null $navigationGroup = 'Контент сайта';

    protected static ?string $modelLabel = 'Город';

    protected static ?string $pluralModelLabel = 'Города';

    protected static ?int $navigationSort = 9;

    public static function getPages(): array
    {
        return ['index' => ListCity::route('/'), 'create' => CreateCity::route('/create'), 'edit' => EditCity::route('/{record}/edit')];
    }
}

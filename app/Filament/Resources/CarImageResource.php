<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateCarImage;
use App\Filament\Resources\Pages\EditCarImage;
use App\Filament\Resources\Pages\ListCarImage;
use App\Models\CarImage;

class CarImageResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = CarImage::class;

    protected static ?string $slug = 'photos';

    protected static string|\UnitEnum|null $navigationGroup = 'Автопарк';

    protected static ?string $modelLabel = 'Фотография';

    protected static ?string $pluralModelLabel = 'Фотографии';

    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return ['index' => ListCarImage::route('/'), 'create' => CreateCarImage::route('/create'), 'edit' => EditCarImage::route('/{record}/edit')];
    }
}

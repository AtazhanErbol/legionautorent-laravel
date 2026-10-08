<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateMenuLink;
use App\Filament\Resources\Pages\EditMenuLink;
use App\Filament\Resources\Pages\ListMenuLink;
use App\Models\MenuLink;

class MenuLinkResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = MenuLink::class;

    protected static ?string $slug = 'navigation';

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?string $modelLabel = 'Ссылка меню';

    protected static ?string $pluralModelLabel = 'Навигация';

    protected static ?int $navigationSort = 16;

    public static function getPages(): array
    {
        return ['index' => ListMenuLink::route('/'), 'create' => CreateMenuLink::route('/create'), 'edit' => EditMenuLink::route('/{record}/edit')];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateSiteSettings;
use App\Filament\Resources\Pages\EditSiteSettings;
use App\Filament\Resources\Pages\ListSiteSettings;
use App\Models\SiteSettings;

class SiteSettingsResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = SiteSettings::class;

    protected static ?string $slug = 'site-settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?string $modelLabel = 'Настройки сайта';

    protected static ?string $pluralModelLabel = 'Настройки сайта';

    protected static ?int $navigationSort = 13;

    public static function getPages(): array
    {
        return ['index' => ListSiteSettings::route('/'), 'create' => CreateSiteSettings::route('/create'), 'edit' => EditSiteSettings::route('/{record}/edit')];
    }
}

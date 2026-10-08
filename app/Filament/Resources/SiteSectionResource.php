<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateSiteSection;
use App\Filament\Resources\Pages\EditSiteSection;
use App\Filament\Resources\Pages\ListSiteSection;
use App\Models\SiteSection;

class SiteSectionResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = SiteSection::class;

    protected static ?string $slug = 'sections';

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?string $modelLabel = 'Раздел главной и городов';

    protected static ?string $pluralModelLabel = 'Порядок разделов';

    protected static ?int $navigationSort = 15;

    public static function getPages(): array
    {
        return ['index' => ListSiteSection::route('/'), 'create' => CreateSiteSection::route('/create'), 'edit' => EditSiteSection::route('/{record}/edit')];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreatePage;
use App\Filament\Resources\Pages\EditPage;
use App\Filament\Resources\Pages\ListPage;
use App\Models\Page;

class PageResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = Page::class;

    protected static ?string $slug = 'pages';

    protected static string|\UnitEnum|null $navigationGroup = 'Контент сайта';

    protected static ?string $modelLabel = 'Страница';

    protected static ?string $pluralModelLabel = 'Страницы';

    protected static ?int $navigationSort = 10;

    public static function getPages(): array
    {
        return ['index' => ListPage::route('/'), 'create' => CreatePage::route('/create'), 'edit' => EditPage::route('/{record}/edit')];
    }
}

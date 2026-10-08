<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateRedirect;
use App\Filament\Resources\Pages\EditRedirect;
use App\Filament\Resources\Pages\ListRedirect;
use App\Models\Redirect;

class RedirectResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = Redirect::class;

    protected static ?string $slug = 'redirects';

    protected static string|\UnitEnum|null $navigationGroup = 'Переводы и SEO';

    protected static ?string $modelLabel = 'Перенаправление';

    protected static ?string $pluralModelLabel = 'Перенаправления';

    protected static ?int $navigationSort = 18;

    public static function getPages(): array
    {
        return ['index' => ListRedirect::route('/'), 'create' => CreateRedirect::route('/create'), 'edit' => EditRedirect::route('/{record}/edit')];
    }
}

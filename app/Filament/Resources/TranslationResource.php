<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateTranslation;
use App\Filament\Resources\Pages\EditTranslation;
use App\Filament\Resources\Pages\ListTranslation;
use App\Models\Translation;

class TranslationResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = Translation::class;

    protected static ?string $slug = 'translations';

    protected static string|\UnitEnum|null $navigationGroup = 'Переводы и SEO';

    protected static ?string $modelLabel = 'Перевод';

    protected static ?string $pluralModelLabel = 'Переводы KZ / EN';

    protected static ?int $navigationSort = 17;

    public static function getPages(): array
    {
        return ['index' => ListTranslation::route('/'), 'create' => CreateTranslation::route('/create'), 'edit' => EditTranslation::route('/{record}/edit')];
    }
}

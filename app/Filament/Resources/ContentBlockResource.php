<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateContentBlock;
use App\Filament\Resources\Pages\EditContentBlock;
use App\Filament\Resources\Pages\ListContentBlock;
use App\Models\ContentBlock;

class ContentBlockResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = ContentBlock::class;

    protected static ?string $slug = 'content-blocks';

    protected static string|\UnitEnum|null $navigationGroup = 'Контент сайта';

    protected static ?string $modelLabel = 'Блок контента';

    protected static ?string $pluralModelLabel = 'Блоки контента';

    protected static ?int $navigationSort = 12;

    public static function getPages(): array
    {
        return ['index' => ListContentBlock::route('/'), 'create' => CreateContentBlock::route('/create'), 'edit' => EditContentBlock::route('/{record}/edit')];
    }
}

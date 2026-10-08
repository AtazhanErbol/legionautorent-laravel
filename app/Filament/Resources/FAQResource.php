<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateFAQ;
use App\Filament\Resources\Pages\EditFAQ;
use App\Filament\Resources\Pages\ListFAQ;
use App\Models\FAQ;

class FAQResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = FAQ::class;

    protected static ?string $slug = 'faqs';

    protected static string|\UnitEnum|null $navigationGroup = 'Контент сайта';

    protected static ?string $modelLabel = 'Вопрос и ответ';

    protected static ?string $pluralModelLabel = 'Вопросы и ответы';

    protected static ?int $navigationSort = 11;

    public static function getPages(): array
    {
        return ['index' => ListFAQ::route('/'), 'create' => CreateFAQ::route('/create'), 'edit' => EditFAQ::route('/{record}/edit')];
    }
}

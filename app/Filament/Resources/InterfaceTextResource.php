<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateInterfaceText;
use App\Filament\Resources\Pages\EditInterfaceText;
use App\Filament\Resources\Pages\ListInterfaceText;
use App\Models\InterfaceText;

class InterfaceTextResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = InterfaceText::class;

    protected static ?string $slug = 'interface-texts';

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?string $modelLabel = 'Подпись интерфейса';

    protected static ?string $pluralModelLabel = 'Тексты и кнопки';

    protected static ?int $navigationSort = 14;

    public static function getPages(): array
    {
        return ['index' => ListInterfaceText::route('/'), 'create' => CreateInterfaceText::route('/create'), 'edit' => EditInterfaceText::route('/{record}/edit')];
    }
}

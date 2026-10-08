<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateGroup;
use App\Filament\Resources\Pages\EditGroup;
use App\Filament\Resources\Pages\ListGroup;
use App\Models\Group;

class GroupResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = Group::class;

    protected static ?string $slug = 'groups';

    protected static string|\UnitEnum|null $navigationGroup = 'Доступ и роли';

    protected static ?string $modelLabel = 'группа';

    protected static ?string $pluralModelLabel = 'группы';

    protected static ?int $navigationSort = 20;

    public static function getPages(): array
    {
        return ['index' => ListGroup::route('/'), 'create' => CreateGroup::route('/create'), 'edit' => EditGroup::route('/{record}/edit')];
    }
}

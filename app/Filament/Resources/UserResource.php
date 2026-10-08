<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateUser;
use App\Filament\Resources\Pages\EditUser;
use App\Filament\Resources\Pages\ListUser;
use App\Models\User;

class UserResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static string|\UnitEnum|null $navigationGroup = 'Доступ и роли';

    protected static ?string $modelLabel = 'пользователь';

    protected static ?string $pluralModelLabel = 'пользователи';

    protected static ?int $navigationSort = 19;

    public static function getPages(): array
    {
        return ['index' => ListUser::route('/'), 'create' => CreateUser::route('/create'), 'edit' => EditUser::route('/{record}/edit')];
    }
}

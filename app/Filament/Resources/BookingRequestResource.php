<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Pages\CreateBookingRequest;
use App\Filament\Resources\Pages\EditBookingRequest;
use App\Filament\Resources\Pages\ListBookingRequest;
use App\Models\BookingRequest;

class BookingRequestResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = BookingRequest::class;

    protected static ?string $slug = 'leads';

    protected static string|\UnitEnum|null $navigationGroup = 'Заявки';

    protected static ?string $modelLabel = 'Заявка на аренду';

    protected static ?string $pluralModelLabel = 'Заявки на аренду';

    protected static ?int $navigationSort = 0;

    public static function getPages(): array
    {
        return ['index' => ListBookingRequest::route('/'), 'create' => CreateBookingRequest::route('/create'), 'edit' => EditBookingRequest::route('/{record}/edit')];
    }
}

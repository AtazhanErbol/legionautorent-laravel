<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\BookingRequestResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateBookingRequest extends CreateRecord
{
    protected static string $resource = BookingRequestResource::class;
}

<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\BookingRequestResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditBookingRequest extends EditRecord
{
    protected static string $resource = BookingRequestResource::class;
}

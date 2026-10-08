<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\BookingRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditBookingRequest extends EditRecord
{
    protected static string $resource = BookingRequestResource::class;
}

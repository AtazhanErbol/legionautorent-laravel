<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRequest extends CmsModel
{
    protected $table = 'bookings_bookingrequest';

    protected $casts = ['consent' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'start_date' => 'date', 'end_date' => 'date'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

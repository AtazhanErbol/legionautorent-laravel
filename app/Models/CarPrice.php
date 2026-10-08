<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarPrice extends CmsModel
{
    protected $table = 'cars_carprice';

    protected $casts = ['min_days' => 'integer', 'max_days' => 'integer', 'daily_price' => 'decimal:0', 'deposit' => 'decimal:0', 'mileage_limit' => 'integer'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

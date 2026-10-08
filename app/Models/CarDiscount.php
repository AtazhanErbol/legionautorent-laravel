<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarDiscount extends CmsModel
{
    protected $table = 'cars_cardiscount';

    protected $casts = ['min_days' => 'integer', 'max_days' => 'integer', 'percent' => 'integer'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

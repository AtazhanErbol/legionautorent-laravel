<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarImage extends CmsModel
{
    protected $table = 'cars_carimage';

    protected $casts = ['card_width' => 'integer', 'card_height' => 'integer', 'is_main' => 'boolean', 'sort_order' => 'integer', 'width' => 'integer', 'height' => 'integer', 'variants' => 'array'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

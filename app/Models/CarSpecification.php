<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarSpecification extends CmsModel
{
    protected $table = 'cars_carspecification';

    protected $casts = ['sort_order' => 'integer'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

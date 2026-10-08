<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FAQ extends CmsModel
{
    protected $table = 'pages_faq';

    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

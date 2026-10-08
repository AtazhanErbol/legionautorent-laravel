<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class CarCategory extends CmsModel
{
    protected $table = 'cars_carcategory';

    protected $casts = ['legacy_meta' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'active' => 'boolean', 'sort_order' => 'integer'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'category_id');
    }
}

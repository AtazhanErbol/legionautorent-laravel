<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends CmsModel
{
    protected $table = 'cars_car';

    protected $casts = ['legacy_meta' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'base_price' => 'decimal:0', 'year' => 'integer', 'seats' => 'integer', 'doors' => 'integer', 'deposit' => 'decimal:0', 'mileage_limit' => 'integer', 'active' => 'boolean', 'featured' => 'boolean', 'accepts_requests' => 'boolean', 'sort_order' => 'integer'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class, 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'category_id');
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'cars_car_cities', 'car_id', 'city_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(CarFeature::class, 'cars_car_features', 'car_id', 'carfeature_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class, 'car_id')->orderByDesc('is_main')->orderBy('sort_order')->orderBy('id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CarPrice::class, 'car_id')->orderBy('min_days')->orderBy('id');
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(CarDiscount::class, 'car_id')->orderBy('min_days')->orderBy('id');
    }

    public function extra_specs(): HasMany
    {
        return $this->hasMany(CarSpecification::class, 'car_id')->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublic(Builder $q): Builder
    {
        return $q->where('active', true)->whereHas('category', fn ($c) => $c->where('active', true))->whereHas('cities', fn ($c) => $c->where('active', true));
    }
}

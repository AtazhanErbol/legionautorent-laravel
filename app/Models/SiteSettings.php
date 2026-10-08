<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSettings extends CmsModel
{
    protected $table = 'core_sitesettings';

    protected $casts = ['hero_video_fps' => 'integer', 'hero_names' => 'array', 'enable_hero_3d' => 'boolean', 'enable_hero_video' => 'boolean', 'hero_placeholder' => 'boolean', 'notifications_enabled' => 'boolean', 'show_partner_section' => 'boolean'];

    public function hero_car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'hero_car_id');
    }
}

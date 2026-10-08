<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends CmsModel
{
    protected $table = 'seo_translation';

    protected $casts = ['object_id' => 'integer', 'published' => 'boolean', 'updated_at' => 'datetime'];

    public function content_type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }
}

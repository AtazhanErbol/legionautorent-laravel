<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends CmsModel
{
    protected $table = 'auth_permission';

    protected $casts = [];

    public function content_type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }
}

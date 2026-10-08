<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogEntry extends CmsModel
{
    protected $table = 'django_admin_log';

    protected $casts = ['action_time' => 'datetime', 'action_flag' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function content_type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }
}

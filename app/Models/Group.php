<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends CmsModel
{
    protected $table = 'auth_group';

    protected $casts = [];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'auth_group_permissions', 'group_id', 'permission_id');
    }
}

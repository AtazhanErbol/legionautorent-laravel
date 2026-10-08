<?php

namespace App\Models;

class MenuLink extends CmsModel
{
    protected $table = 'core_menulink';

    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer'];
}

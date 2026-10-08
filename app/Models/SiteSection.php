<?php

namespace App\Models;

class SiteSection extends CmsModel
{
    protected $table = 'core_sitesection';

    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer'];
}

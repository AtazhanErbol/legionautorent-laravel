<?php

namespace App\Models;

class ContentBlock extends CmsModel
{
    protected $table = 'core_contentblock';

    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer'];
}

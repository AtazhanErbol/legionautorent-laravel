<?php

namespace App\Models;

class Page extends CmsModel
{
    protected $table = 'pages_page';

    protected $casts = ['legacy_meta' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'active' => 'boolean', 'show_in_footer' => 'boolean', 'legal_approved' => 'boolean'];
}

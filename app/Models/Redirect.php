<?php

namespace App\Models;

class Redirect extends CmsModel
{
    protected $table = 'seo_redirect';

    protected $casts = ['status_code' => 'integer', 'active' => 'boolean'];
}

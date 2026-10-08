<?php

namespace App\Providers;

use App\Auth\DjangoCompatibleHasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->useLangPath(resource_path('lang'));
    }

    public function boot(): void
    {
        Hash::extend('django', fn () => new DjangoCompatibleHasher(['rounds' => 12]));
    }
}

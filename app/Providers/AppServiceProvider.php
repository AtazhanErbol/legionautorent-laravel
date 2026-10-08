<?php

namespace App\Providers;

use App\Auth\DjangoCompatibleHasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Hash::extend('django', fn () => new DjangoCompatibleHasher(['rounds' => 12]));
    }
}

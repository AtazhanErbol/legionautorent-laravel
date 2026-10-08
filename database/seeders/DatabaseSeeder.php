<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Artisan::call('legion:import-django', ['file' => resource_path('data/catalogue-snapshot.json')]) !== 0) {
            throw new \RuntimeException('Catalogue seeding refused: '.Artisan::output());
        }
    }
}

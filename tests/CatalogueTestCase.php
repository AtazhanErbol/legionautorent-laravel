<?php

namespace Tests;

use App\Models\CarImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

abstract class CatalogueTestCase extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'mysql' || config('database.connections.mysql.database') !== 'legion_test') {
            throw new \LogicException('Tests refuse to migrate or mutate any database other than legion_test.');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('legion_test', config('database.connections.mysql.database'));
        config(['legion.staging' => false]);
        Storage::fake('media');
        $fixture = UploadedFile::fake()->image('catalogue-fixture.jpg', 32, 20);
        $contents = file_get_contents($fixture->getRealPath());
        foreach (CarImage::query()->pluck('original')->filter()->unique() as $path) {
            Storage::disk('media')->put($path, $contents);
        }

    }

    protected function prepareUrlForRequest($uri): string
    {
        return str_starts_with((string) $uri, 'http') ? (string) $uri : rtrim(config('app.url'), '/').'/'.ltrim((string) $uri, '/');
    }

    protected function owner(): User
    {
        return User::create(['username' => 'test-owner', 'password' => Hash::make('not-a-real-password'), 'is_staff' => true, 'is_active' => true, 'is_superuser' => true]);
    }
}

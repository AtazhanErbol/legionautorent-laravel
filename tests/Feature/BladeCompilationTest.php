<?php

namespace Tests\Feature;

use Tests\TestCase;

class BladeCompilationTest extends TestCase
{
    public function test_every_public_template_compiles_to_valid_php(): void
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))) as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            try {
                $compiled = app('blade.compiler')->compileString(file_get_contents($file->getPathname()));
                token_get_all($compiled, TOKEN_PARSE);
                $this->assertDoesNotMatchRegularExpression('/@(if|else|endif|section|endsection|show|php|endphp|__raw_block_)[a-zA-Z_(]/', $compiled, $file->getPathname());
            } catch (\ParseError $error) {
                $this->fail($file->getPathname().': '.$error->getMessage());
            }
        }
        $this->assertTrue(true);
    }
}

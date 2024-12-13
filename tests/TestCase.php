<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTempFileSystem();
    }

    protected function tearDown(): void
    {
        $this->destroyTempFileSystem();
        parent::tearDown();
    }

    protected function setupTempFileSystem(): void
    {
        foreach (config('filesystems.disks') as $name => $config) {
            $config['driver'] = 'local';
            $config['root'] = __DIR__ . '/Support/Files/' . $name;
            File::ensureDirectoryExists($config['root']);
            config()->set("filesystems.disks.{$name}", $config);
        }
    }

    protected function destroyTempFileSystem(): void {
        $directories = File::directories(__DIR__ . '/Support/Files/');
        foreach ($directories as $directory) {
            File::deleteDirectory($directory);
        }
    }

}

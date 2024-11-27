<?php

namespace Nomiai\PhpSdk\Laravel\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as Orchestra;
use Nomiai\PhpSdk\Laravel\NomiAIServiceProvider;

class TestCase extends Orchestra
{
    /**
     * Sets up any custom environment variables.
     */
    public function getEnvironmentSetUp(mixed $app): void
    {
        Config::set('database.default', 'testing');
    }

    /**
     * Register package providers.
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [
            NomiAIServiceProvider::class,
        ];
    }
}

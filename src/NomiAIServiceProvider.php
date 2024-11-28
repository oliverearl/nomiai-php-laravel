<?php

declare(strict_types=1);

namespace Nomiai\PhpSdk\Laravel;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException;
use Nomiai\PhpSdk\NomiAI;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NomiAIServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nomiai-php-laravel')
            ->hasConfigFile('nomiai');
    }

    /**
     * @throws \Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException
     */
    public function registeringPackage(): void
    {
        $this->app->bind(NomiAI::class, function (): NomiAI {
            $token = Config::string('nomiai.api_key');
            $endpoint = Config::string('nomiai.endpoint');

            if (empty($token)) {
                throw NomiaiException::missingApiToken();
            }

            if (empty($endpoint)) {
                throw NomiaiException::missingEndpoint();
            }

            return new NomiAI($token, Str::rtrim($endpoint, '/'));
        });
    }
}

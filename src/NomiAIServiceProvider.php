<?php

declare(strict_types=1);

namespace Nomiai\PhpSdk\Laravel;

use Illuminate\Support\Facades\Config;
use Nomiai\PhpSdk\Laravel\Adapters\LaravelHttpGuzzleAdapter;
use Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException;
use Nomiai\PhpSdk\NomiAI;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NomiAIServiceProvider extends PackageServiceProvider
{
    /**
     * The version of the Laravel SDK.
     */
    final public const string VERSION = '1.0.0';

    /**
     * Configure the package.
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nomiai-php-laravel')
            ->hasConfigFile('nomiai');
    }

    /**
     * Register bindings into the container.
     *
     * @throws \Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException
     */
    public function registeringPackage(): void
    {
        $this->app->bind(NomiAI::class, function (): NomiAI {
            $token = Config::string('nomiai.api_key');
            $endpoint = Config::string('nomiai.endpoint') ?: NomiAI::DEFAULT_ENDPOINT;

            if (empty($token)) {
                throw NomiaiException::missingApiToken();
            }

            return new NomiAI(
                token: $token,
                endpoint: $endpoint,
                client: new LaravelHttpGuzzleAdapter(['token' => $token, 'base_url' => $endpoint]),
            );
        });
    }
}

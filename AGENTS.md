# AI Agent Guide for nomiai-php-laravel

## Project Overview

This is a **Laravel wrapper package** for the [Nomi.ai PHP SDK](https://github.com/oliverearl/nomiai-php). It's a Laravel service provider package that bridges the standalone SDK with Laravel's ecosystem, not a standalone application. The wrapper provides Laravel-specific integration (facades, config, HTTP adapter) while delegating actual API functionality to the base SDK.

**Key Architecture Pattern**: Adapter pattern - `LaravelHttpGuzzleAdapter` (in `src/Adapters/`) implements `GuzzleHttp\ClientInterface` to wrap Laravel's `Http` facade, enabling Laravel's HTTP mocking features (`Http::fake()`) to work with the Guzzle-based SDK.

## Critical Conventions

### Strict Typing Everywhere
**Every file** must begin with `declare(strict_types=1);` after the opening PHP tag. This is enforced by Pest architecture tests in `tests/ArchTest.php`. The test suite will fail without it.

### Code Style: PER Standard
Uses Laravel Pint with PER (PHP Evolving Recommendations) preset. Run `composer format` before committing. The preset is explicitly configured in `pint.json` as `"preset": "per"`.

### Namespace Convention
All classes use the namespace `Nomiai\PhpSdk\Laravel\*` (note the capitalization: `PhpSdk` not `PHPSdk`). This matches the base SDK's namespace pattern.

## Development Workflows

### Testing with Pest
```bash
composer test              # Run full test suite
composer test-coverage     # With coverage report
```

Tests use Orchestra Testbench (see `tests/TestCase.php`) to simulate a Laravel environment. The base `TestCase` automatically sets `nomiai.api_key` to `'token'` in `getEnvironmentSetUp()`.

### Static Analysis
```bash
composer analyse  # PHPStan (Larastan) level 5
```

PHPStan ignores the `larastan.noEnvCallsOutsideOfConfig` error for `config/nomiai.php` (see `phpstan.neon.dist`). This is intentional - `env()` calls are only allowed in config files per Laravel conventions.

### Architecture Testing
`tests/ArchTest.php` enforces:
- Laravel, PHP, and security presets
- Strict equality (`===` not `==`)
- Strict types declaration
- Namespace-based rules

## Key Integration Points

### Service Provider Registration (`src/NomiAIServiceProvider.php`)
The provider binds `Nomiai\PhpSdk\NomiAI::class` (the base SDK class) into Laravel's container with:
1. Config-driven API key/endpoint from `config/nomiai.php`
2. Custom `LaravelHttpGuzzleAdapter` injected as the HTTP client
3. Throws `NomiaiException::missingApiToken()` if `NOMIAI_API_KEY` is empty

### Facade Pattern (`src/Facades/NomiAI.php`)
Thin facade that proxies to the base SDK class. Uses `@mixin` PHPDoc to provide IDE autocomplete for all base SDK methods. The facade accessor returns `BaseNomiAI::class`, not a string literal.

### Configuration (`config/nomiai.php`)
Two keys only:
- `api_key`: from `NOMIAI_API_KEY` env var
- `endpoint`: from `NOMIAI_ENDPOINT` env var (defaults to `https://api.nomi.ai`)

## HTTP Adapter Implementation

The `LaravelHttpGuzzleAdapter` bridges two HTTP client paradigms:

**Critical**: It implements `send()` and `request()` methods differently:
- `send()`: Converts PSR-7 `RequestInterface` → Laravel HTTP request → PSR-7 `Response`
- `request()`: Directly uses Laravel's `Http::send()` with method/URI/options

Both methods:
- Set `Authorization` header from options array (`token` key)
- Force `Accept: application/json` and `Content-Type: application/json`
- Include custom User-Agent: `Nomi.ai Laravel SDK (VERSION)`
- Use `RequestOptions::HTTP_ERRORS => false` to prevent exceptions on HTTP errors

## Exception Handling

Only one custom exception: `NomiaiException extends RuntimeException`
- Has static factory `missingApiToken()` 
- Thrown when API key is empty/missing
- Used in both service provider registration and adapter

## Package Publishing

This package auto-discovers via `composer.json` extra config:
```json
"extra": {
    "laravel": {
        "providers": ["NomiAIServiceProvider"],
        "aliases": {"NomiAI": "NomiAI"}
    }
}
```

Config can be published: `php artisan vendor:publish --tag="nomiai-php-laravel-config"`

## Version Support Matrix

- PHP: `^8.3` (requires JSON extension)
- Laravel: `^11.0 || ^12.0 || ^13.0`
- Base SDK: `oliverearl/nomiai-php` `^v1.0.3`

Check `composer.json` before adding new dependencies or changing minimum versions.

## When Modifying Code

1. **Always** add `declare(strict_types=1);` to new files
2. Run `composer format` after changes (auto-formats to PER)
3. Run `composer test` - includes architecture rule validation
4. If touching the adapter: test with `Http::fake()` to ensure mocking still works
5. Update `NomiAIServiceProvider::VERSION` constant for releases

## Common Pitfalls

- Don't add `env()` calls outside `config/nomiai.php` - Larastan will flag it
- Don't use loose comparison (`==`) - architecture tests enforce strict equality
- Don't inject `Request` or other Laravel HTTP classes into the adapter - it must implement Guzzle's `ClientInterface`
- The facade proxies to `\Nomiai\PhpSdk\NomiAI` (base SDK), not a Laravel-specific class


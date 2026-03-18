<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Nomiai\PhpSdk\Laravel\Adapters\LaravelHttpGuzzleAdapter;
use Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException;

it('throws exception when token is missing in request method', function (): void {
    $adapter = new LaravelHttpGuzzleAdapter(['base_url' => 'https://api.example.com']);

    $adapter->request('GET', '/test');
})->throws(NomiaiException::class, 'No Nomi.ai API token was provided. Please provide an API token!');

it('throws exception when token is missing in send method', function (): void {
    $adapter = new LaravelHttpGuzzleAdapter(['base_url' => 'https://api.example.com']);
    $request = new Request('GET', '/test');

    $adapter->send($request);
})->throws(NomiaiException::class, 'No Nomi.ai API token was provided. Please provide an API token!');

it('handles error responses correctly', function (): void {
    Http::fake(['*' => Http::response(['error' => 'Not Found'], 404)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test');

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getBody()->getContents())->toContain('Not Found');
});

it('handles server error responses', function (): void {
    Http::fake(['*' => Http::response(['error' => 'Internal Server Error'], 500)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test');

    expect($response->getStatusCode())->toBe(500);
});

it('sends requests with query parameters', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test', [
        'query' => ['foo' => 'bar', 'baz' => 'qux'],
    ]);

    expect($response->getStatusCode())->toBe(200);

    Http::assertSent(function (HttpRequest $request): bool {
        return $request->url() === 'https://api.example.com/test?foo=bar&baz=qux';
    });
});

it('sends requests with custom headers', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test', [
        'headers' => ['X-Custom-Header' => 'custom-value'],
    ]);

    expect($response->getStatusCode())->toBe(200);

    Http::assertSent(function (HttpRequest $request): bool {
        return $request->hasHeader('X-Custom-Header')
            && $request->header('X-Custom-Header')[0] === 'custom-value';
    });
});

it('sends requests with body', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 201)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $body = json_encode(['name' => 'Test', 'value' => 123]);

    $response = $adapter->request('POST', '/test', [
        'body' => $body,
    ]);

    expect($response->getStatusCode())->toBe(201);

    Http::assertSent(function (HttpRequest $request) use ($body): bool {
        return $request->body() === $body;
    });
});

it('filters out null headers', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test', [
        'headers' => ['X-Valid' => 'value', 'X-Null' => null],
    ]);

    expect($response->getStatusCode())->toBe(200);

    Http::assertSent(function (HttpRequest $request): bool {
        return $request->hasHeader('X-Valid')
            && ! $request->hasHeader('X-Null');
    });
});

it('includes authorization header', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'my-secret-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test');

    expect($response->getStatusCode())->toBe(200);

    Http::assertSent(function (HttpRequest $request): bool {
        return $request->hasHeader('Authorization')
            && $request->header('Authorization')[0] === 'my-secret-token';
    });
});

it('includes json content type and accept headers', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test');

    expect($response->getStatusCode())->toBe(200);

    Http::assertSent(function (HttpRequest $request): bool {
        return $request->hasHeader('Accept')
            && $request->header('Accept')[0] === 'application/json'
            && $request->hasHeader('Content-Type')
            && $request->header('Content-Type')[0] === 'application/json';
    });
});

it('sends psr7 requests via send method', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $request = new Request('POST', '/test', [], 'test body');
    $response = $adapter->send($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getBody()->getContents())->toContain('success');
});

it('returns correct config value from getConfig', function (): void {
    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'custom' => 'value',
        'base_url' => 'https://api.example.com',
    ]);

    expect($adapter->getConfig('custom'))->toBe('value')
        ->and($adapter->getConfig('token'))->toBe('test-token')
        ->and($adapter->getConfig('nonexistent'))->toBeNull();
});

it('returns all config when no option specified', function (): void {
    $config = [
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ];

    $adapter = new LaravelHttpGuzzleAdapter($config);

    expect($adapter->getConfig())->toBe($config);
});

it('throws BadMethodCallException for sendAsync', function (): void {
    $adapter = new LaravelHttpGuzzleAdapter(['token' => 'test-token']);
    $request = new Request('GET', '/test');

    $adapter->sendAsync($request);
})->throws(BadMethodCallException::class, 'Not implemented.');

it('throws BadMethodCallException for requestAsync', function (): void {
    $adapter = new LaravelHttpGuzzleAdapter(['token' => 'test-token']);

    $adapter->requestAsync('GET', '/test');
})->throws(BadMethodCallException::class, 'Not implemented.');

it('includes custom user agent header', function (): void {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $adapter = new LaravelHttpGuzzleAdapter([
        'token' => 'test-token',
        'base_url' => 'https://api.example.com',
    ]);

    $response = $adapter->request('GET', '/test');

    expect($response->getStatusCode())->toBe(200);

    Http::assertSent(function (HttpRequest $request): bool {
        return $request->hasHeader('User-Agent')
            && str_contains($request->header('User-Agent')[0], 'Nomi.ai Laravel SDK');
    });
});

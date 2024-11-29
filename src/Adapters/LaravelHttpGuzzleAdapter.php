<?php

declare(strict_types=1);

namespace Nomiai\PhpSdk\Laravel\Adapters;

use BadMethodCallException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException;
use Nomiai\PhpSdk\Laravel\NomiAIServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LaravelHttpGuzzleAdapter
 *
 * This adapter enables us to make full use of the Laravel HTTP facade within the PHP SDK, including support for
 * the Http facade, and the use of `Http::fake()`.
 *
 * @see https://laravel.com/docs/master/mocking#mocking-facades
 */
final class LaravelHttpGuzzleAdapter implements ClientInterface
{
    /**
     * LaravelHttpGuzzleAdapter constructor.
     *
     * @param array<string, mixed> $options
     */
    public function __construct(private readonly array $options) {}

    /** @inheritDoc */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        // Convert the Guzzle PSR-7 request to a Laravel HTTP request.
        $response = Http::withOptions($options)
            ->baseUrl(Arr::get($this->options, 'base_url'))
            ->withUserAgent(sprintf('Nomi.ai Laravel SDK (%s)', NomiAIServiceProvider::VERSION))
            ->send(
                $request->getMethod(),
                (string) $request->getUri(),
                [
                    RequestOptions::BODY => $request->getBody()->getContents(),
                    RequestOptions::HEADERS => array_merge(
                        [
                            'Authorization' => Arr::get($this->options, 'token'),
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                        $request->getHeaders(),
                    ),
                    RequestOptions::HTTP_ERRORS => false,
                ],
            );

        // Convert the Laravel HTTP response back to a PSR-7 response.
        return new Response(
            $response->status(),
            $response->headers(),
            $response->body(),
        );
    }

    /** @inheritDoc */
    public function request(string $method, $uri, array $options = []): ResponseInterface
    {
        // Extract the body and headers from the options.
        $body = $options['body'] ?? null;
        $headers = $options['headers'] ?? [];
        $query = $options['query'] ?? [];

        $mergedHeaders = array_merge(
            [
                'Authorization' => Arr::get($this->options, 'token') ?: throw NomiaiException::missingApiToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            $headers,
        );

        // Remove null headers to avoid invalid header issues.
        $filteredHeaders = array_filter($mergedHeaders, fn(mixed $value): bool => $value !== null);

        $laravelOptions = [
            'base_url' => Arr::get($this->options, 'base_url'),
            'headers' => $filteredHeaders,
            'body' => $body,
            'query' => $query,
            RequestOptions::HTTP_ERRORS => false,
        ];

        // Make the request using Laravel's HTTP client.
        $response = Http::withOptions($laravelOptions)
            ->baseUrl(Arr::get($this->options, 'base_url'))
            ->withUserAgent(sprintf('Nomi.ai Laravel SDK (%s)', NomiAIServiceProvider::VERSION))
            ->send($method, $uri);

        // Convert the Laravel HTTP response back to a PSR-7 response.
        return new Response(
            $response->status(),
            $response->headers(),
            $response->body(),
        );
    }

    /** @inheritDoc */
    public function getConfig(mixed $option = null): mixed
    {
        return Arr::get($this->options, $option);
    }

    /** @inheritDoc */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        throw new BadMethodCallException('Not implemented.');
    }


    /** @inheritDoc **/
    public function requestAsync(string $method, $uri, array $options = []): PromiseInterface
    {
        throw new BadMethodCallException('Not implemented.');
    }
}

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Nomiai\PhpSdk\Enums\Gender;
use Nomiai\PhpSdk\Enums\RelationshipType;
use Nomiai\PhpSdk\Laravel\Facades\NomiAI;
use Nomiai\PhpSdk\Resources\Nomi;

it('can fire a http request and be successfully mocked by laravel', function (): void {
    $nomi = Nomi::make([
        'uuid' => fake()->uuid(),
        'name' => fake()->name(),
        'created' => new DateTimeImmutable(),
        'gender' => fake()->randomElement(Gender::cases()),
        'relationshipType' => fake()->randomElement(RelationshipType::cases()),
    ]);

    Http::preventingStrayRequests();
    Http::fake(['*' => Http::response($nomi->toArray())]);

    $response = NomiAI::getNomi($nomi->uuid);

    expect($response)
        ->toBeInstanceOf(Nomi::class)
        ->and($response->toArray())->toEqual($nomi->toArray());
});

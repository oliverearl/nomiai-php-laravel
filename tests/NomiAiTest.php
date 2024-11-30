<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Nomiai\PhpSdk\Laravel\Exceptions\NomiaiException;
use Nomiai\PhpSdk\Laravel\Facades\NomiAI;

it('can get a new nomi instance', function (): void {
    Config::set('nomiai.api_key', 'token');
    $token = NomiAI::token();

    expect($token)->toEqual('token');
});

it('will throw an exception if an empty api key is provided', function (): void {
    Config::set('nomiai.api_key', '');
    $token = NomiAI::endpoint();
})->throws(NomiaiException::class);

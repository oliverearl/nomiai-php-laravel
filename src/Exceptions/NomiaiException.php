<?php

declare(strict_types=1);

namespace Nomiai\PhpSdk\Laravel\Exceptions;

use RuntimeException;

class NomiaiException extends RuntimeException
{
    /**
     * The exception to be thrown if an API token is not provided.
     */
    public static function missingApiToken(): self
    {
        return new self('No Nomi.ai API token was provided. Please provide an API token!');
    }
}

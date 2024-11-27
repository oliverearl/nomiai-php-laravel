<?php

declare(strict_types=1);

namespace Nomiai\PhpSdk\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Nomiai\PhpSdk\NomiAI as BaseNomiAI;

/**
 * @see \Nomiai\PhpSdk\NomiAI
 *
 * @mixin \Nomiai\PhpSdk\NomiAI
 */
class NomiAI extends Facade
{
    /** {@inheritDoc} */
    protected static function getFacadeAccessor(): string
    {
        return BaseNomiAI::class;
    }
}

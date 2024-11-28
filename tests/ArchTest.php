<?php

declare(strict_types=1);

arch()->preset()->laravel();
arch()->preset()->php();
arch()->preset()->security();

expect('\Nomiai\PhpSdk\Laravel')
    ->toUseStrictEquality()
    ->toUseStrictTypes();

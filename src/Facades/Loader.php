<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Twirelab\LaravelRouter\Classes\Loader group(array<string, mixed> $options)
 * @method static void loadControllers(mixed $controllers)
 * @method static void loadFromDirectories(mixed $path)
 *
 * @mixin \Twirelab\LaravelRouter\Classes\Loader
 */
class Loader extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-router';
    }
}

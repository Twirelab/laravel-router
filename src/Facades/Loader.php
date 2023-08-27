<?php

namespace Twirelab\LaravelRouter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Twirelab\LaravelRouter\Classes\Loader group(array $options)
 * @method static void loadControllers(mixed $controllers)
 * @method static void loadFromDirectories(mixed $path)
 */
class Loader extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-router';
    }
}

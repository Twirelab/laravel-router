<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Twirelab\LaravelRouter\Loaders\ClassLoader;
use Twirelab\LaravelRouter\Loaders\DirectoryLoader;

class Loader
{
    /**
     * Load selected controllers.
     */
    public static function loadControllers(mixed $controllers): void
    {
        App::make(ClassLoader::class)->load(
            Arr::wrap($controllers)
        );
    }

    /**
     * Load controllers from directories.
     */
    public static function loadFromDirectories(mixed $path): void
    {
        App::make(DirectoryLoader::class)->load(
            Arr::wrap($path)
        );
    }
}

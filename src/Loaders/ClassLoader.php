<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Loaders;

use Illuminate\Support\Arr;
use Twirelab\LaravelRouter\Interfaces\Loader as LoaderInterface;
use Twirelab\LaravelRouter\Traits\Loader;

class ClassLoader implements LoaderInterface
{
    use Loader;

    /**
     * Load routes.
     */
    public function load(mixed $source): void
    {
        foreach (Arr::wrap($source) as $controller) {
            $this->loadController($controller);
        }
    }
}

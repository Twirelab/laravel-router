<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Classes;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Twirelab\LaravelRouter\Loaders\ClassLoader;
use Twirelab\LaravelRouter\Loaders\DirectoryLoader;

final class Loader
{
    public function __construct(
        protected array | null $group = null,
    ) {
    }

    /**
     * Create a main group.
     */
    public function group(array $options): Loader
    {
        $this->group = $options;

        return $this;
    }

    /**
     * Load selected controllers.
     *
     * @throws Exception
     */
    public function loadControllers(mixed $controllers): void
    {
        $this->loader(ClassLoader::class, $controllers);
    }

    /**
     * Load controllers from directories.
     *
     * @throws Exception
     */
    public function loadFromDirectories(mixed $path): void
    {
        $this->loader(DirectoryLoader::class, $path);
    }

    /**
     * @throws Exception
     */
    private function loader(mixed $loader, mixed $path): void
    {
        if(is_null($this->group)) {
            App::make($loader)->load(Arr::wrap($path));
            return;
        }

        Route::group($this->group, fn() => App::make($loader)->load(Arr::wrap($path)));
    }
}

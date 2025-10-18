<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter;

use Illuminate\Support\ServiceProvider;
use Twirelab\LaravelRouter\Classes\Loader;

class LaravelRouterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            abstract: 'laravel-router',
            concrete: fn() => new Loader()
        );
    }
}

<?php

namespace Twirelab\LaravelRouter;

use Illuminate\Support\ServiceProvider;

class LaravelRouterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/../config/router.php',
            key: 'router',
        );
    }
}
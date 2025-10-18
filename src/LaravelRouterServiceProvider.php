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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-router.php' => config_path('laravel-router.php'),
            ], 'laravel-router-config');

            $this->commands([
                \Twirelab\LaravelRouter\Commands\ShowAvailableRoutes::class,
                \Twirelab\LaravelRouter\Commands\ShowUnavailableRoutes::class,
            ]);
        }
    }
}

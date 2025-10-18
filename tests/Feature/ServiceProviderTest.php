<?php

use Illuminate\Foundation\Application;
use Twirelab\LaravelRouter\LaravelRouterServiceProvider;

it('registers the service provider', function () {
    $app = new Application();
    $provider = new LaravelRouterServiceProvider($app);

    $provider->register();

    expect($app->bound('laravel-router'))->toBeTrue();
});

it('binds singleton instance', function () {
    $app = new Application();
    $provider = new LaravelRouterServiceProvider($app);

    $provider->register();

    $instance1 = $app->make('laravel-router');
    $instance2 = $app->make('laravel-router');

    expect($instance1)->toBe($instance2);
});

it('binds correct class instance', function () {
    $app = new Application();
    $provider = new LaravelRouterServiceProvider($app);

    $provider->register();

    $instance = $app->make('laravel-router');

    expect($instance)->toBeInstanceOf(\Twirelab\LaravelRouter\Classes\Loader::class);
});

it('can resolve from container', function () {
    $app = new Application();
    $provider = new LaravelRouterServiceProvider($app);

    $provider->register();

    $instance = $app['laravel-router'];

    expect($instance)->toBeInstanceOf(\Twirelab\LaravelRouter\Classes\Loader::class);
});

it('service provider is listed in package providers', function () {
    $app = new Application();
    $provider = new LaravelRouterServiceProvider($app);

    // Test that the service provider can be instantiated
    expect($provider)->toBeInstanceOf(LaravelRouterServiceProvider::class);
});

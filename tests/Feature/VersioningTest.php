<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Methods;
use Twirelab\LaravelRouter\Enums\Version;

beforeEach(function () {
    Route::getRoutes()->refreshNameLookups();
    Route::getRoutes()->refreshActionLookups();
});

it('registers routes with neutral version', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: Version::NEUTRAL)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('test');
    // expect($route->getAction('laravel_router_version'))->toBe(Version::NEUTRAL);
});

it('registers routes with active version', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: 1)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('v1/test');
    // expect($route->getAction('laravel_router_version'))->toBe(1);
});

it('skips routes with inactive version', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: 3)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(0);
});

it('inherits version from controller to method', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new #[Router(version: 1)] class {
        #[Method('/test', Methods::GET)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('v1/test');
    // expect($route->getAction('laravel_router_version'))->toBe(1);
});

it('method version overrides controller version', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new #[Router(version: 1)] class {
        #[Method('/test', Methods::GET, version: 2)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('v2/test');
    // expect($route->getAction('laravel_router_version'))->toBe(2);
});

it('uses custom version prefix', function () {
    config([
        'laravel-router.active_versions' => [1, 2],
        'laravel-router.version_prefix' => 'api'
    ]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: 1)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('api1/test');
});

it('disables version in url', function () {
    config([
        'laravel-router.active_versions' => [1, 2],
        'laravel-router.version_in_url' => false
    ]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: 1)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('test');
});

it('handles empty version prefix', function () {
    config([
        'laravel-router.active_versions' => [1, 2],
        'laravel-router.version_prefix' => ''
    ]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: 1)]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $routes = Route::getRoutes();
    expect(count($routes->getRoutes()))->toBe(1);

    $route = $routes->getRoutes()[0];
    expect($route->uri())->toBe('1/test');
});

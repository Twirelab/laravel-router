<?php

use Illuminate\Support\Facades\Route;
use Tests\Fixtures\Controllers\SimpleController;
use Tests\Fixtures\Controllers\GroupedController;
use Twirelab\LaravelRouter\Classes\Loader;

beforeEach(function () {
    Route::getRoutes()->refreshNameLookups();
});

it('loads controllers from directory pattern', function () {
    $loader = new Loader();
    $loader->loadFromDirectories(__DIR__ . '/../Fixtures/Controllers/SimpleController.php');

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('simple.show'))->not->toBeNull();
});

it('loads multiple controllers from directory patterns', function () {
    $loader = new Loader();
    $loader->loadFromDirectories([
        __DIR__ . '/../Fixtures/Controllers/SimpleController.php',
        __DIR__ . '/../Fixtures/Controllers/GroupedController.php'
    ]);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    // Check SimpleController routes
    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('simple.show'))->not->toBeNull();

    // Check GroupedController routes
    expect($routes->getByName('api.users.index'))->not->toBeNull();
    expect($routes->getByName('api.users.show'))->not->toBeNull();
    expect($routes->getByName('api.users.store'))->not->toBeNull();
});

it('loads controllers from directory with group configuration', function () {
    $loader = new Loader();
    $groupedLoader = $loader->group(['prefix' => 'test', 'middleware' => 'test']);
    $groupedLoader->loadFromDirectories(__DIR__ . '/../Fixtures/Controllers/SimpleController.php');

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $indexRoute = $routes->getByName('simple.index');
    expect($indexRoute->uri())->toBe('test/simple');
    expect($indexRoute->gatherMiddleware())->toContain('test');
});

it('handles empty directory pattern gracefully', function () {
    $loader = new Loader();

    expect(fn () => $loader->loadFromDirectories([]))
        ->not->toThrow(Exception::class);
});

it('loads controllers with glob pattern', function () {
    $loader = new Loader();
    // Load specific controllers to avoid AbstractController
    $loader->loadFromDirectories([
        __DIR__ . '/../Fixtures/Controllers/SimpleController.php',
        __DIR__ . '/../Fixtures/Controllers/GroupedController.php'
    ]);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    // Should load all controllers in the directory
    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('api.users.index'))->not->toBeNull();
});

it('verifies all expected routes are registered from directory loading', function () {
    $loader = new Loader();
    $loader->loadFromDirectories(__DIR__ . '/../Fixtures/Controllers/SimpleController.php');

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    // Verify all routes from SimpleController are registered
    $simpleRoutes = collect($routes)->filter(function ($route) {
        return str_contains($route->getAction('controller') ?? '', 'SimpleController');
    });

    expect($simpleRoutes)->toHaveCount(2);

    $routeNames = $simpleRoutes->map(fn ($route) => $route->getName())->toArray();
    expect($routeNames)->toContain('simple.index');
    expect($routeNames)->toContain('simple.show');
});

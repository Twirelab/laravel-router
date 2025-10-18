<?php

use Illuminate\Support\Facades\Route;
use Tests\Fixtures\Controllers\SimpleController;
use Tests\Fixtures\Controllers\GroupedController;
use Tests\Fixtures\Controllers\ComplexController;
use Tests\Fixtures\Controllers\EmptyController;
use Twirelab\LaravelRouter\Classes\Loader;

beforeEach(function () {
    Route::getRoutes()->refreshNameLookups();
});

it('registers simple routes from controller', function () {
    $loader = new Loader();
    $loader->loadControllers(SimpleController::class);

    // Refresh name lookups after loading routes
    Route::getRoutes()->refreshNameLookups();

    $routes = Route::getRoutes();

    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('simple.show'))->not->toBeNull();

    $indexRoute = $routes->getByName('simple.index');
    expect($indexRoute->uri())->toBe('simple');
    expect($indexRoute->methods())->toContain('GET');
    expect($indexRoute->getAction('controller'))->toBe(SimpleController::class . '@index');

    $showRoute = $routes->getByName('simple.show');
    expect($showRoute->uri())->toBe('simple/{id}');
    expect($showRoute->methods())->toContain('GET');
    expect($showRoute->getAction('controller'))->toBe(SimpleController::class . '@show');
});

it('registers routes with group configuration', function () {
    $loader = new Loader();
    $loader->loadControllers(GroupedController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    expect($routes->getByName('api.users.index'))->not->toBeNull();
    expect($routes->getByName('api.users.show'))->not->toBeNull();
    expect($routes->getByName('api.users.store'))->not->toBeNull();

    $indexRoute = $routes->getByName('api.users.index');
    expect($indexRoute->uri())->toBe('api/v1/users');
    expect($indexRoute->methods())->toContain('GET');
    expect($indexRoute->getAction('controller'))->toBe(GroupedController::class . '@index');

    $showRoute = $routes->getByName('api.users.show');
    expect($showRoute->uri())->toBe('api/v1/users/{id}');
    expect($showRoute->methods())->toContain('GET');
    expect($showRoute->getAction('controller'))->toBe(GroupedController::class . '@show');
});

it('registers routes with middleware', function () {
    $loader = new Loader();
    $loader->loadControllers(GroupedController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $indexRoute = $routes->getByName('api.users.index');
    expect($indexRoute->gatherMiddleware())->toContain('auth');

    $storeRoute = $routes->getByName('api.users.store');
    expect($storeRoute->gatherMiddleware())->toContain('auth');
    expect($storeRoute->gatherMiddleware())->toContain('admin');
});

it('registers routes with where constraints', function () {
    $loader = new Loader();
    $loader->loadControllers(GroupedController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $showRoute = $routes->getByName('api.users.show');
    $wheres = $showRoute->wheres;
    expect($wheres['id'])->toBe('[0-9]+');
});

it('registers routes with domain configuration', function () {
    $loader = new Loader();
    $loader->loadControllers(ComplexController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $dashboardRoute = $routes->getByName('admin.dashboard');
    expect($dashboardRoute->getDomain())->toBe('admin.example.com');
    expect($dashboardRoute->uri())->toBe('admin/dashboard');
});

it('registers routes with different HTTP methods', function () {
    $loader = new Loader();
    $loader->loadControllers(ComplexController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $usersRoute = $routes->getByName('admin.users.index');
    expect($usersRoute->methods())->toContain('GET');

    $updateRoute = $routes->getByName('admin.users.update');
    expect($updateRoute->methods())->toContain('PUT');

    $deleteRoute = $routes->getByName('admin.users.destroy');
    expect($deleteRoute->methods())->toContain('DELETE');

    $settingsRoute = $routes->getByName('admin.settings');
    expect($settingsRoute->methods())->toContain('GET');
    expect($settingsRoute->methods())->toContain('POST');
    expect($settingsRoute->methods())->toContain('PUT');
    expect($settingsRoute->methods())->toContain('DELETE');
});

it('does not register routes for controller without annotations', function () {
    $loader = new Loader();
    $loader->loadControllers(EmptyController::class);

    $routes = Route::getRoutes();

    expect($routes->getByName('empty.index'))->toBeNull();
    expect($routes->getByName('empty.show'))->toBeNull();
});

it('registers routes with custom group configuration', function () {
    $loader = new Loader();
    $groupedLoader = $loader->group(['prefix' => 'custom', 'middleware' => 'custom']);
    $groupedLoader->loadControllers(SimpleController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $indexRoute = $routes->getByName('simple.index');
    expect($indexRoute->uri())->toBe('custom/simple');
    expect($indexRoute->gatherMiddleware())->toContain('custom');
});

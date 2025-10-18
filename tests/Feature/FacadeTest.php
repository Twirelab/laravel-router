<?php

use Illuminate\Support\Facades\Route;
use Tests\Fixtures\Controllers\SimpleController;
use Twirelab\LaravelRouter\Facades\Loader;

beforeEach(function () {
    Route::getRoutes()->refreshNameLookups();
});

it('can use facade to load controllers', function () {
    Loader::loadControllers(SimpleController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('simple.show'))->not->toBeNull();
});

it('can use facade to load from directories', function () {
    Loader::loadFromDirectories(__DIR__ . '/../Fixtures/Controllers/SimpleController.php');

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('simple.show'))->not->toBeNull();
});

it('can use facade to create groups', function () {
    $groupedLoader = Loader::group(['prefix' => 'facade', 'middleware' => 'facade']);
    $groupedLoader->loadControllers(SimpleController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $indexRoute = $routes->getByName('simple.index');
    expect($indexRoute->uri())->toBe('facade/simple');
    expect($indexRoute->gatherMiddleware())->toContain('facade');
});

it('facade returns correct instance', function () {
    $loader = Loader::group(['prefix' => 'test']);

    expect($loader)->toBeInstanceOf(\Twirelab\LaravelRouter\Classes\Loader::class);
});

it('facade can chain methods', function () {
    Loader::group(['prefix' => 'chained'])
        ->loadControllers(SimpleController::class);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    $indexRoute = $routes->getByName('simple.index');
    expect($indexRoute->uri())->toBe('chained/simple');
});

it('facade handles multiple controllers', function () {
    Loader::loadControllers([
        SimpleController::class,
        'Tests\Fixtures\Controllers\GroupedController'
    ]);

    Route::getRoutes()->refreshNameLookups();
    $routes = Route::getRoutes();

    expect($routes->getByName('simple.index'))->not->toBeNull();
    expect($routes->getByName('api.users.index'))->not->toBeNull();
});

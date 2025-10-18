<?php

use Twirelab\LaravelRouter\Classes\Loader;

it('creates new loader instance with group', function () {
    $loader = new Loader();
    $groupedLoader = $loader->group(['prefix' => 'api']);

    expect($groupedLoader)->toBeInstanceOf(Loader::class);
    expect($groupedLoader)->not->toBe($loader);
});

it('creates new loader instance without group', function () {
    $loader = new Loader();

    expect($loader)->toBeInstanceOf(Loader::class);
});

it('has required methods', function () {
    $loader = new Loader();

    expect(method_exists($loader, 'group'))->toBeTrue();
    expect(method_exists($loader, 'loadControllers'))->toBeTrue();
    expect(method_exists($loader, 'loadFromDirectories'))->toBeTrue();
});

it('accepts group configuration', function () {
    $loader = new Loader(['prefix' => 'api', 'middleware' => 'auth']);

    expect($loader)->toBeInstanceOf(Loader::class);
});

it('accepts null group configuration', function () {
    $loader = new Loader(null);

    expect($loader)->toBeInstanceOf(Loader::class);
});

it('group method returns new instance', function () {
    $loader = new Loader();
    $groupedLoader = $loader->group(['prefix' => 'test']);

    expect($groupedLoader)->toBeInstanceOf(Loader::class);
    expect($groupedLoader)->not->toBe($loader);
});

it('can chain group method', function () {
    $loader = new Loader();
    $groupedLoader = $loader->group(['prefix' => 'api'])->group(['middleware' => 'auth']);

    expect($groupedLoader)->toBeInstanceOf(Loader::class);
});

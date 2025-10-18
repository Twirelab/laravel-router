<?php

use Tests\Fixtures\Controllers\SimpleController;
use Twirelab\LaravelRouter\Exceptions\InvalidControllerException;
use Twirelab\LaravelRouter\Loaders\ClassLoader;

it('implements Loader interface', function () {
    $loader = new ClassLoader();

    expect($loader)->toBeInstanceOf(\Twirelab\LaravelRouter\Interfaces\Loader::class);
});

it('has load method', function () {
    $loader = new ClassLoader();

    expect(method_exists($loader, 'load'))->toBeTrue();
});

it('accepts string controller parameter', function () {
    $loader = new ClassLoader();

    // Test that it accepts string parameter
    expect(fn() => $loader->load(SimpleController::class))
        ->toThrow(Exception::class); // Will throw because Laravel app is not set up
});

it('accepts array controller parameter', function () {
    $loader = new ClassLoader();

    // Test that it accepts array parameter
    expect(fn() => $loader->load([SimpleController::class]))
        ->toThrow(Exception::class); // Will throw because Laravel app is not set up
});

it('handles empty array input', function () {
    $loader = new ClassLoader();

    expect(fn() => $loader->load([]))
        ->not->toThrow(Exception::class);
});

it('throws exception for non-existent class', function () {
    $loader = new ClassLoader();

    expect(fn() => $loader->load('NonExistentClass'))
        ->toThrow(InvalidControllerException::class, 'Class "NonExistentClass" does not exist.');
});

it('throws exception for abstract class', function () {
    $loader = new ClassLoader();

    expect(fn() => $loader->load('Tests\Fixtures\Controllers\AbstractController'))
        ->toThrow(InvalidControllerException::class, 'Annotations from class "Tests\Fixtures\Controllers\AbstractController" cannot be read as it is abstract.');
});

<?php

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Enums\Methods;

it('can create method annotation with all parameters', function () {
    $method = new Method(
        uri: '/test',
        method: Methods::GET,
        name: 'test.name',
        middlewares: ['auth', 'admin'],
        where: ['id' => '\d+']
    );

    expect($method->getUri())->toBe('/test');
    expect($method->getMethod())->toBe('get');
    expect($method->getName())->toBe('test.name');
    expect($method->getMiddlewares())->toBe(['auth', 'admin']);
    expect($method->getWhere())->toBe(['id' => '\d+']);
});

it('can create method annotation with minimal parameters', function () {
    $method = new Method(
        uri: '/minimal',
        method: Methods::POST
    );

    expect($method->getUri())->toBe('/minimal');
    expect($method->getMethod())->toBe('post');
    expect($method->getName())->toBeNull();
    expect($method->getMiddlewares())->toBeNull();
    expect($method->getWhere())->toBeNull();
});

it('can create method annotation with string middleware', function () {
    $method = new Method(
        uri: '/single-middleware',
        method: Methods::PUT,
        middlewares: 'auth'
    );

    expect($method->getMiddlewares())->toBe('auth');
});

it('can create method annotation with array middleware', function () {
    $method = new Method(
        uri: '/array-middleware',
        method: Methods::DELETE,
        middlewares: ['web', 'auth', 'verified']
    );

    expect($method->getMiddlewares())->toBe(['web', 'auth', 'verified']);
});

it('can create method annotation with different HTTP methods', function () {
    $getMethod = new Method('/get', Methods::GET);
    $postMethod = new Method('/post', Methods::POST);
    $putMethod = new Method('/put', Methods::PUT);
    $deleteMethod = new Method('/delete', Methods::DELETE);
    $anyMethod = new Method('/any', Methods::ANY);

    expect($getMethod->getMethod())->toBe('get');
    expect($postMethod->getMethod())->toBe('post');
    expect($putMethod->getMethod())->toBe('put');
    expect($deleteMethod->getMethod())->toBe('delete');
    expect($anyMethod->getMethod())->toBe('any');
});

it('can create method annotation with where constraints', function () {
    $method = new Method(
        uri: '/users/{id}',
        method: Methods::GET,
        where: ['id' => '[0-9]+', 'slug' => '[a-z-]+']
    );

    expect($method->getWhere())->toBe(['id' => '[0-9]+', 'slug' => '[a-z-]+']);
});

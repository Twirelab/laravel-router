<?php

use Twirelab\LaravelRouter\Annotations\Router;

it('can create router annotation with all parameters', function () {
    $router = new Router(
        as: 'api.',
        prefix: 'api/v1',
        domain: 'api.example.com',
        middlewares: ['api', 'throttle']
    );

    expect($router->getName())->toBe('api.');
    expect($router->getPrefix())->toBe('api/v1');
    expect($router->getDomain())->toBe('api.example.com');
    expect($router->getMiddleware())->toBe(['api', 'throttle']);
});

it('can create router annotation with minimal parameters', function () {
    $router = new Router();

    expect($router->getName())->toBeNull();
    expect($router->getPrefix())->toBeNull();
    expect($router->getDomain())->toBeNull();
    expect($router->getMiddleware())->toBe([]);
});

it('can create router annotation with string middleware', function () {
    $router = new Router(
        prefix: 'admin',
        middlewares: 'auth'
    );

    expect($router->getPrefix())->toBe('admin');
    expect($router->getMiddleware())->toBe('auth');
});

it('can create router annotation with array middleware', function () {
    $router = new Router(
        prefix: 'dashboard',
        middlewares: ['web', 'auth', 'verified']
    );

    expect($router->getPrefix())->toBe('dashboard');
    expect($router->getMiddleware())->toBe(['web', 'auth', 'verified']);
});

it('can create router annotation with only prefix', function () {
    $router = new Router(prefix: 'api');

    expect($router->getPrefix())->toBe('api');
    expect($router->getName())->toBeNull();
    expect($router->getDomain())->toBeNull();
    expect($router->getMiddleware())->toBe([]);
});

it('can create router annotation with only name', function () {
    $router = new Router(as: 'admin.');

    expect($router->getName())->toBe('admin.');
    expect($router->getPrefix())->toBeNull();
    expect($router->getDomain())->toBeNull();
    expect($router->getMiddleware())->toBe([]);
});

it('can create router annotation with only domain', function () {
    $router = new Router(domain: 'subdomain.example.com');

    expect($router->getDomain())->toBe('subdomain.example.com');
    expect($router->getName())->toBeNull();
    expect($router->getPrefix())->toBeNull();
    expect($router->getMiddleware())->toBe([]);
});

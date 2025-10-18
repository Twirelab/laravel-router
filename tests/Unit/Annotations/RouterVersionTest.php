<?php

declare(strict_types=1);

use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Version;

it('can create router annotation with version', function () {
    $router = new Router(version: 1);

    expect($router->getVersion())->toBe(1);
});

it('can create router annotation with version enum', function () {
    $router = new Router(version: Version::NEUTRAL);

    expect($router->getVersion())->toBe(Version::NEUTRAL);
});

it('can create router annotation without version', function () {
    $router = new Router();

    expect($router->getVersion())->toBeNull();
});

it('can create router annotation with all parameters including version', function () {
    $router = new Router(
        as: 'api',
        prefix: 'api',
        domain: 'api.example.com',
        middlewares: ['auth'],
        version: 2
    );

    expect($router->getName())->toBe('api');
    expect($router->getPrefix())->toBe('api');
    expect($router->getDomain())->toBe('api.example.com');
    expect($router->getMiddleware())->toBe(['auth']);
    expect($router->getVersion())->toBe(2);
});

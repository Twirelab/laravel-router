<?php

declare(strict_types=1);

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Enums\Methods;
use Twirelab\LaravelRouter\Enums\Version;

it('can create method annotation with version', function () {
    $method = new Method('/test', Methods::GET, version: 1);

    expect($method->getVersion())->toBe(1);
});

it('can create method annotation with version enum', function () {
    $method = new Method('/test', Methods::GET, version: Version::NEUTRAL);

    expect($method->getVersion())->toBe(Version::NEUTRAL);
});

it('can create method annotation without version', function () {
    $method = new Method('/test', Methods::GET);

    expect($method->getVersion())->toBeNull();
});

it('can create method annotation with all parameters including version', function () {
    $method = new Method(
        uri: '/test/{id}',
        method: Methods::GET,
        name: 'test.show',
        middlewares: ['auth'],
        where: ['id' => '[0-9]+'],
        version: 2
    );

    expect($method->getUri())->toBe('/test/{id}');
    expect($method->getMethod())->toBe('get');
    expect($method->getName())->toBe('test.show');
    expect($method->getMiddlewares())->toBe(['auth']);
    expect($method->getWhere())->toBe(['id' => '[0-9]+']);
    expect($method->getVersion())->toBe(2);
});

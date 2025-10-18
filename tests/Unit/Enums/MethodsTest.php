<?php

use Twirelab\LaravelRouter\Enums\Methods;

it('has all expected HTTP method cases', function () {
    $cases = Methods::cases();

    expect($cases)->toHaveCount(5);
    expect($cases)->toContain(Methods::GET);
    expect($cases)->toContain(Methods::POST);
    expect($cases)->toContain(Methods::PUT);
    expect($cases)->toContain(Methods::DELETE);
    expect($cases)->toContain(Methods::ANY);
});

it('has correct string values for each method', function () {
    expect(Methods::GET->value)->toBe('get');
    expect(Methods::POST->value)->toBe('post');
    expect(Methods::PUT->value)->toBe('put');
    expect(Methods::DELETE->value)->toBe('delete');
    expect(Methods::ANY->value)->toBe('any');
});

it('can be created from string value', function () {
    expect(Methods::from('get'))->toBe(Methods::GET);
    expect(Methods::from('post'))->toBe(Methods::POST);
    expect(Methods::from('put'))->toBe(Methods::PUT);
    expect(Methods::from('delete'))->toBe(Methods::DELETE);
    expect(Methods::from('any'))->toBe(Methods::ANY);
});

it('throws exception for invalid string value', function () {
    expect(fn() => Methods::from('invalid'))
        ->toThrow(ValueError::class);
});

it('can be created from string value with tryFrom', function () {
    expect(Methods::tryFrom('get'))->toBe(Methods::GET);
    expect(Methods::tryFrom('post'))->toBe(Methods::POST);
    expect(Methods::tryFrom('invalid'))->toBeNull();
});

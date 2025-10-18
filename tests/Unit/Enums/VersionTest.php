<?php

declare(strict_types=1);

use Twirelab\LaravelRouter\Enums\Version;

it('has neutral version', function () {
    expect(Version::NEUTRAL->getValue())->toBe(0);
    expect(Version::NEUTRAL->isNeutral())->toBeTrue();
});

it('can create version from integer', function () {
    $version = Version::fromInt(1);
    expect($version->getValue())->toBe(1);
    expect($version->isNeutral())->toBeFalse();
});

it('can create version from integer 2', function () {
    $version = Version::fromInt(2);
    expect($version->getValue())->toBe(2);
    expect($version->isNeutral())->toBeFalse();
});

it('can create version from integer 3', function () {
    $version = Version::fromInt(3);
    expect($version->getValue())->toBe(3);
    expect($version->isNeutral())->toBeFalse();
});

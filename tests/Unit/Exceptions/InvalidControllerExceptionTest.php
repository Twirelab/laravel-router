<?php

use Twirelab\LaravelRouter\Exceptions\InvalidControllerException;

it('creates classNotFound exception with correct message', function () {
    $exception = InvalidControllerException::classNotFound('NonExistentClass');

    expect($exception)->toBeInstanceOf(InvalidControllerException::class);
    expect($exception->getMessage())->toBe('Class "NonExistentClass" does not exist.');
});

it('creates abstractClass exception with correct message', function () {
    $exception = InvalidControllerException::abstractClass('AbstractController');

    expect($exception)->toBeInstanceOf(InvalidControllerException::class);
    expect($exception->getMessage())->toBe('Annotations from class "AbstractController" cannot be read as it is abstract.');
});

it('creates invalidGlobPattern exception with correct message', function () {
    $exception = InvalidControllerException::invalidGlobPattern('/invalid/path/*');

    expect($exception)->toBeInstanceOf(InvalidControllerException::class);
    expect($exception->getMessage())->toBe('Invalid glob pattern "/invalid/path/*".');
});

it('creates noClassInFile exception with correct message', function () {
    $exception = InvalidControllerException::noClassInFile('/path/to/file.php');

    expect($exception)->toBeInstanceOf(InvalidControllerException::class);
    expect($exception->getMessage())->toBe('No class found in file "/path/to/file.php".');
});

it('creates fileReadError exception with correct message', function () {
    $exception = InvalidControllerException::fileReadError('/path/to/file.php');

    expect($exception)->toBeInstanceOf(InvalidControllerException::class);
    expect($exception->getMessage())->toBe('Unable to read file "/path/to/file.php".');
});

it('extends InvalidArgumentException', function () {
    $exception = InvalidControllerException::classNotFound('TestClass');

    expect($exception)->toBeInstanceOf(InvalidArgumentException::class);
});

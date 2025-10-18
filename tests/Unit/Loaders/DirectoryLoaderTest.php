<?php

use Twirelab\LaravelRouter\Exceptions\InvalidControllerException;
use Twirelab\LaravelRouter\Loaders\DirectoryLoader;

it('implements Loader interface', function () {
    $loader = new DirectoryLoader();

    expect($loader)->toBeInstanceOf(\Twirelab\LaravelRouter\Interfaces\Loader::class);
});

it('has load method', function () {
    $loader = new DirectoryLoader();

    expect(method_exists($loader, 'load'))->toBeTrue();
});

it('accepts array parameter', function () {
    $loader = new DirectoryLoader();

    // Test that it accepts array parameter
    expect(fn () => $loader->load([__DIR__ . '/../../Fixtures/Controllers/SimpleController.php']))
        ->toThrow(Exception::class); // Will throw because Laravel app is not set up
});

it('handles empty glob results', function () {
    $loader = new DirectoryLoader();

    // Test with a pattern that returns empty results (not false)
    expect(fn () => $loader->load(['/invalid/path/that/does/not/exist/*']))
        ->not->toThrow(Exception::class);
});

it('throws exception for non-readable file', function () {
    $loader = new DirectoryLoader();

    // Create a temporary file that we can't read
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    chmod($tempFile, 0000); // Remove all permissions

    expect(fn () => $loader->load([$tempFile]))
        ->toThrow(InvalidControllerException::class, "Unable to read file \"{$tempFile}\".");

    // Clean up
    chmod($tempFile, 0644);
    unlink($tempFile);
});

it('throws exception for file without class', function () {
    $loader = new DirectoryLoader();

    // Create a temporary file without a class
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tempFile, '<?php echo "no class here";');

    expect(fn () => $loader->load([$tempFile]))
        ->toThrow(InvalidControllerException::class, "No class found in file \"{$tempFile}\".");

    unlink($tempFile);
});

it('extracts class name from file without namespace', function () {
    $loader = new DirectoryLoader();

    // Create a temporary file with a class but no namespace
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tempFile, '<?php class TestClass { }');

    // Use reflection to test the private method
    $reflection = new ReflectionClass($loader);
    $method = $reflection->getMethod('getClassFromPath');
    $method->setAccessible(true);

    $className = $method->invoke($loader, $tempFile);

    expect($className)->toBe('TestClass');

    unlink($tempFile);
});

it('extracts class name with namespace from file', function () {
    $loader = new DirectoryLoader();

    // Create a temporary file with a class and namespace
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tempFile, '<?php namespace Tests\Fixtures; class TestClass { }');

    // Use reflection to test the private method
    $reflection = new ReflectionClass($loader);
    $method = $reflection->getMethod('getClassFromPath');
    $method->setAccessible(true);

    $className = $method->invoke($loader, $tempFile);

    expect($className)->toBe('Tests\Fixtures\TestClass');

    unlink($tempFile);
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Enums\Methods;

beforeEach(function () {
    Route::getRoutes()->refreshNameLookups();
    Route::getRoutes()->refreshActionLookups();
});

it('shows available routes command', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new class () {
        #[Method('/test', Methods::GET, version: 1, name: 'test.index')]
        public function test()
        {
            return 'test';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $this->artisan('laravel-router:list')
        ->expectsOutput('URL')
        ->expectsOutput('Method')
        ->expectsOutput('Name')
        ->expectsOutput('Controller')
        ->expectsOutput('Version')
        ->expectsOutput('/v1/test')
        ->expectsOutput('GET')
        ->expectsOutput('test.index')
        ->assertExitCode(0);
});

it('shows unavailable routes command', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    // Create a temporary controller file for testing
    $controllerContent = '<?php

namespace Tests\Fixtures\Controllers;

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Enums\Methods;

class TestUnavailableController
{
    #[Method("/unavailable", Methods::GET, version: 3, name: "unavailable.test")]
    public function test()
    {
        return "unavailable";
    }
}';

    $tempFile = sys_get_temp_dir() . '/TestUnavailableController.php';
    file_put_contents($tempFile, $controllerContent);

    // Include the file so class exists
    require_once $tempFile;

    $this->artisan('laravel-router:unavailable', ['--path' => sys_get_temp_dir()])
        ->expectsOutput('URL')
        ->expectsOutput('Method')
        ->expectsOutput('Name')
        ->expectsOutput('Controller')
        ->expectsOutput('Version')
        ->expectsOutput('Reason')
        ->expectsOutput('/unavailable')
        ->expectsOutput('GET')
        ->expectsOutput('unavailable.test')
        ->assertExitCode(0);

    // Clean up
    unlink($tempFile);
});

it('filters routes by version in available routes command', function () {
    config(['laravel-router.active_versions' => [1, 2]]);

    $controller = new class () {
        #[Method('/test1', Methods::GET, version: 1, name: 'test1')]
        public function test1()
        {
            return 'test1';
        }

        #[Method('/test2', Methods::GET, version: 2, name: 'test2')]
        public function test2()
        {
            return 'test2';
        }
    };

    (new \Twirelab\LaravelRouter\Classes\Loader())->loadControllers([get_class($controller)]);

    $this->artisan('laravel-router:list', ['--version' => '1'])
        ->expectsOutput('/v1/test1')
        ->doesntExpectOutput('/v2/test2')
        ->assertExitCode(0);
});

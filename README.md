# Laravel Router

> Attention! This package is not suitable for use in production.

The router is a new way of defining routes in the Laravel framework using annotations.

**Requirements**
- Laravel 8 or above.
- PHP 8.1 or above.

## Installation
1. Install the package via composer
```shell
composer require twirelab/laravel-router
```

2. Done! It was simple.

## Usage
### Laravel 10 and below
In the place where you define routes (ex. `RouteServiceProvider`) you need to call a **Loader** class from the package.

The default class:
```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
```

Change to this:
```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Twirelab\LaravelRouter\Facades\Loader;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Loader::group([
                'prefix' => 'api',
                'middleware' => 'api',
            ])->loadFromDirectories(
                app_path('Http/Controllers/API/**/*Controller.php'),
            );

            Loader::group([
                'middleware' => 'web',
            ])->loadFromDirectories(
                app_path('Http/Controllers/*Controller.php'),
            );
        });
    }
}

```

From now, the Loader automatically imports Controllers from selected directories.

If you prefer, select controllers manually all the time. You can use the `loadControllers` method.

```php
use Twirelab/LaravelRouter/Loader;

Loader::group([
    'prefix' => 'api',
    'middleware' => 'api',
])->loadControllers(
    App\Http\Controllers\FirstController::class,
);

// or

Loader::group([
    'prefix' => 'api',
    'middleware' => 'api',
])->loadControllers(
    App\Http\Controllers\FirstController::class,
    App\Http\Controllers\SecondController::class,
);
```

If don't want to use a group function (for example: you don't need a "main" group
like API or Web) you can use rest of functions directly.

```php
use Twirelab/LaravelRouter/Facades/Loader;

Loader::loadFromDirectories(
    app_path('Http/Controllers/**/*Controller.php')
);

// or

Loader::loadControllers(
    App\Http\Controllers\FirstController::class,
);

```

### Laravel 11 and below
In the place where you define routes (ex. `api.php` or `web.php`) you need to call a **Loader** class from the package.

For example:
```php
<?php

use Twirelab\LaravelRouter\Facades\Loader;

Loader::group([
    'as' => 'v1.',
    'prefix' => 'v1',
])->loadFromDirectories(
    app_path('Http/Controllers/V1/**/*Controller.php')
);

// or

Loader::group([
    'as' => 'v1.',
    'prefix' => 'v1',
])->loadControllers(
    App\Http\Controllers\FirstController::class,
);

```

### Controller
If you want routes to load properly, you need to add the annotate to the controller class.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Twirelab\LaravelRouter\Annotations\Router;

#[Router]
class FirstController extends Controller
{
    // ... methods
}
```

> The "route" annotation works as a group function in Laravel.

**Available options for Router annotation:**
- _as_ - the name of a group,
- _prefix_ - the prefix of a group,
- _domain_ - the domain of a group,
- _middlewares_ - the list of middlewares of a group,

Now, we can define the first route for the method.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Methods;

#[Router]
class FirstController extends Controller
{
    #[Method(uri: '/', method: Methods::GET)]
    public function index()
    {
        // ... logic of the method
    }
}
```

Our route: `GET - / - index > FirstController@index`

**Available options for Method annotation:**
- _uri_ - the address URL for a route,
- _method_ - the method of a route,
- _name_ - the name of a route,
- _middlewares_ - the list of middlewares of a route,
- _where_ - the list of where's of a route,

## Contributing
Feel free to add a new issue! Please describe in detail your problem or idea and I will check your issue and respond - Thank you!

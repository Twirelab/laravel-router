# Laravel Router

> Attention! This package is not suitable for use in production.

Router is a new way of define routes in Laravel framework using annotations.

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
### Provider
In the place where you define a routes (ex. `RouteServiceProvider`) you need call a **Loader** class from package.

```php
use Twirelab/LaravelRouter/Loader;

Loader::loadFromDirectories(
    app_path('Http/Controllers/**/*Controllers.php')
);

// or

Loader::loadFromDirectories([
    app_path('Http/Controllers/Admin/**/*Controllers.php'),
    app_path('Http/Controllers/Client/**/*Controllers.php'),
    // ...etc
]);
```

From now, the Loader automatically import Controllers from selected directory/directories.

If you prefer, select controllers manually all the time. You can use the `loadControllers` method.

```php
use Twirelab/LaravelRouter/Loader;

Loader::loadControllers(
    App\Http\Controllers\FirstController::class,
);

// or

Loader::loadControllers([
    App\Http\Controllers\FirstController::class,
    App\Http\Controllers\SecondController::class,
    // ...etc
]);
```

### Controller
If you want routes to load properly, you need to add the annotate to the controller class.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Twirelab\LaravelRouter\Annotations\Router;

#[Router()]
class FirstController extends Controller
{
    // ... methods
}
```

> The "route" annotation working as group function in Laravel.

Now, we can define the first route for method.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Methods;

#[Router()]
class FirstController extends Controller
{
    #[Method(uri: '/', method: Methods::GET)]
    public function index()
    {
        // ... logic of method
    }
}
```

Our route: `GET - / - index > FirstController@index`

**Available options for Method annotation:**
- _uri_ - the address url for a route,
- _method_ - the method of a route,
- _name_ - the name of a route,
- _middlewares_ - the list of middlewares of a route,
- _where_ - the list of where's of a route,

## Contributing
Feel free to add a new issue! Please describe in detail your problem or idea and I will check your issue and respond - Thank you!
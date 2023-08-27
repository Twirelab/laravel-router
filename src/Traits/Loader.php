<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Traits;

use Illuminate\Routing\Router as LaravelRouter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;

trait Loader
{
    /**
     * Load a controller.
     */
    public function loadController(string $source): void
    {
        if (! class_exists($source)) {
            throw new InvalidArgumentException(
                message: sprintf('Class "%s" does not exist.', $source)
            );
        }

        $class = new ReflectionClass($source);
        if ($class->isAbstract()) {
            throw new InvalidArgumentException(
                message: sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName())
            );
        }

        $controller = $this->getController($class);

        Route::group(
            $controller,
            fn (LaravelRouter $router) => $this->loadMethods(
                router: $router,
                methods: $class->getMethods(),
                data: $controller,
                class: $class
            )
        );
    }

    /**
     * Set a controller data.
     */
    private function setControllerData(string $as = null, string $prefix = null, string $domain = null, string|array $middlewares = null): array
    {
        return compact('as', 'prefix', 'domain', 'middlewares');
    }

    /**
     * Get a parent controller.
     */
    private function getController(ReflectionClass $class): array
    {
        $data = $this->setControllerData();
        $annotation = null;

        if ($attrs = $class->getAttributes(Router::class, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
            $annotation = $attrs->newInstance();
        }

        if ($annotation) {
            $data = $this->setControllerData(
                as: $annotation->getName(),
                prefix: $annotation->getPrefix(),
                domain: $annotation->getDomain(),
                middlewares: $annotation->getMiddlewares()
            );
        }

        return $data;
    }

    /**
     * Load methods from a controller.
     */
    private function loadMethods(LaravelRouter $router, array $methods, array $data, ReflectionClass $class): void
    {
        foreach ($methods as $method) {
            foreach ($this->loadAnnotationMethods($method) as $annotation) {
                $this->addRoute(
                    router: $router,
                    annotation: $annotation,
                    data: $data,
                    class: $class,
                    method: $method
                );
            }
        }
    }

    /**
     * Load annotations methods.
     */
    private function loadAnnotationMethods(ReflectionClass|ReflectionMethod $reflection): iterable
    {
        foreach ($reflection->getAttributes(Method::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            yield $attribute->newInstance();
        }
    }

    /**
     * Add route.
     */
    private function addRoute(LaravelRouter $router, Method $annotation, array $data, ReflectionClass $class, ReflectionMethod $method): void
    {
        $name = $annotation->getName() ?? Str::snake($method->getName());

        $router
            ->{$annotation->getMethod()}($annotation->getUri(), [$class->getName(), $method->getName()])
            ->name($name)
            ->middleware($annotation->getMiddlewares());

        if ($annotation->getWhere()) {
            $router->where($annotation->getWhere());
        }
    }
}

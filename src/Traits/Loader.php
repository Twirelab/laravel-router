<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Traits;

use Illuminate\Routing\Router as LaravelRouter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Versioning;

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

        if(!in_array(
            needle: $controller['version'],
            haystack: Arr::wrap(Config::get('router.acceptable_versions'))
        )) {
            return;
        }

        $this->loadMethods(
            methods: $class->getMethods(),
            controller: $controller,
            class: $class
        );
    }

    /**
     * Set a controller data.
     */
    private function setControllerData(
        string $name = null,
        string $prefix = null,
        string $domain = null,
        string|array $middlewares = null,
        string $version = null
    ): array
    {
        return compact('name', 'prefix', 'domain', 'middlewares', 'version');
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
                name: $annotation->getName(),
                prefix: $annotation->getPrefix(),
                domain: $annotation->getDomain(),
                middlewares: $annotation->getMiddlewares(),
                version: $annotation->getVersion()
            );
        }

        return $data;
    }

    /**
     * Load methods from a controller.
     */
    private function loadMethods(array $methods, array $controller, ReflectionClass $class): void
    {
        foreach ($methods as $method) {
            foreach ($this->loadAnnotationMethods($method) as $annotation) {
                if(in_array(
                    needle: $annotation->getVersion(),
                    haystack: Arr::wrap(Config::get('router.acceptable_versions'))
                )) {
                    continue;
                }

                $this->addRoute(
                    annotation: $annotation,
                    controller: $controller,
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
    private function addRoute(Method $annotation, array $controller, ReflectionClass $class, ReflectionMethod $method): void
    {
        $methodVersion = $annotation->getVersion() === Versioning::NEUTRAL->value ? null : $annotation->getVersion();
        $controllerVersion = $controller['version'] === Versioning::NEUTRAL->value ? null : $annotation['version'];
        $version = $methodVersion ?: $controllerVersion ?: null;
        $uri = $version . $annotation->getUri();

        $name = ($version ? Str::snake($version) . '.' : null) .$controller['name'].($annotation->getName() ?? Str::snake($method->getName()));

        $route = Route::{$annotation->getMethod()}($uri, [$class->getName(), $method->getName()])
            ->name($name)
            ->middleware([
                ...$controller['middlewares'],
                ...$annotation->getMiddlewares(),
            ]);

        if ($annotation->getWhere()) {
            $route->where($annotation->getWhere());
        }
    }
}

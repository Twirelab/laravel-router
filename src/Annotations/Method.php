<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Annotations;

use Twirelab\LaravelRouter\Enums\Methods;

/**
 * Annotation class @Method()
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Method
{
    public function __construct(
        public string $uri,
        public Methods $method,
        public ?string $name = null,
        public string|array|null $middlewares = null,
        public ?array $where = null,
    ) {}

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method->value;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMiddlewares(): string|array|null
    {
        return $this->middlewares;
    }

    public function getWhere(): ?array
    {
        return $this->where;
    }
}

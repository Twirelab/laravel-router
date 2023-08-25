<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Annotations;

use Twirelab\LaravelRouter\Enums\Methods;

use Twirelab\LaravelRouter\Enums\Versioning;

use const http\Client\Curl\VERSIONS;

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
        private readonly string $uri,
        private readonly Methods $method,
        private readonly ?string $name = null,
        private readonly string|array|null $middlewares = null,
        private readonly ?array $where = null,
        private readonly ?string $version = null,
    ) {
    }

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

    public function getVersion(): string
    {
        return $this->version ?? Versioning::NEUTRAL->value;
    }
}

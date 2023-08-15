<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Annotations;

/**
 * Annotation class @Loader()
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Router
{
    public function __construct(
        private readonly ?string $name = null,
        private readonly ?string $prefix = null,
        private readonly ?string $domain = null,
        private readonly string|array|null $middlewares = [],
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getMiddlewares(): array|string|null
    {
        return $this->middlewares;
    }
}

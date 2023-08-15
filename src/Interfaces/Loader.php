<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Interfaces;

interface Loader
{
    /**
     * Load routes.
     */
    public function load(mixed $source): void;
}

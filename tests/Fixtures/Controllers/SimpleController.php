<?php

namespace Tests\Fixtures\Controllers;

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Enums\Methods;

class SimpleController
{
    #[Method('/simple', Methods::GET, name: 'simple.index')]
    public function index(): string
    {
        return 'simple index';
    }

    #[Method('/simple/{id}', Methods::GET, name: 'simple.show')]
    public function show(int $id): string
    {
        return "simple show {$id}";
    }
}

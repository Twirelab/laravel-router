<?php

namespace Tests\Fixtures\Controllers;

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Enums\Methods;

abstract class AbstractController
{
    #[Method('/abstract', Methods::GET)]
    public function index(): string
    {
        return 'abstract index';
    }
}

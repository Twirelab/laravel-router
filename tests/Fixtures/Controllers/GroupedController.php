<?php

namespace Tests\Fixtures\Controllers;

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Methods;

#[Router(prefix: 'api/v1', as: 'api.', middlewares: ['api', 'throttle'])]
class GroupedController
{
    #[Method('/users', Methods::GET, middlewares: ['auth'])]
    public function index(): string
    {
        return 'users index';
    }

    #[Method('/users/{id}', Methods::GET, name: 'users.show', where: ['id' => '[0-9]+'])]
    public function show(int $id): string
    {
        return "user show {$id}";
    }

    #[Method('/users', Methods::POST, middlewares: ['auth', 'admin'])]
    public function store(): string
    {
        return 'user created';
    }
}

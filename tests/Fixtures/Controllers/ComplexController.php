<?php

namespace Tests\Fixtures\Controllers;

use Twirelab\LaravelRouter\Annotations\Method;
use Twirelab\LaravelRouter\Annotations\Router;
use Twirelab\LaravelRouter\Enums\Methods;

#[Router(prefix: 'admin', as: 'admin.', domain: 'admin.example.com', middlewares: ['web', 'auth'])]
class ComplexController
{
    #[Method('/dashboard', Methods::GET, name: 'dashboard')]
    public function dashboard(): string
    {
        return 'admin dashboard';
    }

    #[Method('/users', Methods::GET, name: 'users.index')]
    public function users(): string
    {
        return 'admin users';
    }

    #[Method('/users/{id}', Methods::PUT, name: 'users.update', middlewares: ['admin'], where: ['id' => '\d+'])]
    public function updateUser(int $id): string
    {
        return "updated user {$id}";
    }

    #[Method('/users/{id}', Methods::DELETE, name: 'users.destroy')]
    public function deleteUser(int $id): string
    {
        return "deleted user {$id}";
    }

    #[Method('/settings', Methods::ANY, name: 'settings')]
    public function settings(): string
    {
        return 'admin settings';
    }
}

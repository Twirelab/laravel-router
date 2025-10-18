<?php

namespace Tests\Fixtures\Controllers;

class EmptyController
{
    public function index(): string
    {
        return 'empty index';
    }

    public function show(int $id): string
    {
        return "empty show {$id}";
    }
}

<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Enums;

enum Methods: string
{
    case POST = 'post';
    case GET = 'get';
    case PUT = 'put';
    case DELETE = 'delete';
    case ANY = 'any';
}

<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Enums;

enum Methods: string
{
    case POST = 'POST';
    case GET = 'GET';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case ANY = 'ANY';
}
